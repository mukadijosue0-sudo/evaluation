"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from '/composant/fonction/ajax.js';
import {afficherToast, confirmer, effacerSousLeChamp} from '/composant/fonction/afficher.js';
import {fichierValide, verifierImage} from '/composant/fonction/fichier.js';
import {getData} from '/composant/fonction/page.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Récupération des données depuis le serveur
const lesEtudiants = getData('lesEtudiants');
const parametres = getData('parametres');

// Extraction des paramètres
const {repertoire, maxSize, lesExtensions, maxWidth, maxHeight, resized} = parametres;

// Récupération des éléments de l'interface
const fichier = document.getElementById("fichier");
const lesCartes = document.getElementById("lesCartes");
const tplCarte = document.getElementById("tplCarteEtudiant");

// Identifiant de l'étudiant dont la photo doit être remplacée
let idEtudiant = null;

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// Lancer la fonction controlerFichier si un fichier a été sélectionné dans l'explorateur
fichier.onchange = function () {
    if (fichier.files.length > 0) {
        controlerFichier(fichier.files[0]);
    }
    // permet de sélectionner une deuxième fois le même fichier
    fichier.value = '';
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Crée une carte pour un étudiant.
 *
 * @param {Object} etudiant
 * @returns {HTMLElement}
 */
function creerCarte(etudiant) {
    const carte = tplCarte.content.firstElementChild.cloneNode(true);
    const cible = carte.querySelector(".cible-photo");
    const image = carte.querySelector(".carte-img");
    const zoneVide = carte.querySelector(".photo-vide");
    const boutonSupprimer = carte.querySelector(".btn-supprimer-photo");

    // Identification de la zone.
    cible.id = `cible${etudiant.id}`;

    // Nom et prénom de l'étudiant.
    carte.querySelector(".nom").textContent = etudiant.nom;
    carte.querySelector(".prenom").textContent = etudiant.prenom;

    // Photo.
    image.alt = `Photo de ${etudiant.nomPrenom}`;
    if (etudiant.present) {
        image.src = `${repertoire}/${encodeURIComponent(etudiant.photo)}?t=${Date.now()}`;
        image.hidden = false;
        zoneVide.hidden = true;
        boutonSupprimer.hidden = false;
    } else {
        image.removeAttribute("src");
        image.hidden = true;
        zoneVide.hidden = false;
        boutonSupprimer.hidden = true;
    }


    // Sélection d'une nouvelle photo.
    cible.addEventListener("click", () => {
        idEtudiant = etudiant.id;
        fichier.click();
    });


    // Suppression de la photo.
    boutonSupprimer.addEventListener("click", (event) => {
        // il faut stopper la propagation de l'événement pour éviter que le clic sur le bouton ne déclenche également l'événement de clic sur la carte.
        event.stopPropagation();
        confirmer(() => supprimerPhoto(etudiant.id, image, zoneVide, boutonSupprimer))
    });
    return carte;
}

/**
 * Contrôle le fichier sélectionné au niveau de son extension et de sa taille
 * Contrôle les dimensions de l'image si le redimensionnement n'est pas demandé
 * lancer lad demande de remplacement de l'image
 * @param file {object} fichier à ajouter
 */
function controlerFichier(file) {
    // Efface les erreurs précédentes
    effacerSousLeChamp('fichier');

    // Vérification de taille et d'extension
    if (!fichierValide(file, maxSize, lesExtensions)) {
        return;
    }

    // Vérifications spécifiques pour un fichier image
    // la fonction de rappel reçoit le fichier et l'image éventuellement redimensionnée si le redimensionnement est demandé
    verifierImage(file, resized, maxWidth, maxHeight, (fichierControle) => {
        majPhoto(fichierControle);
    });
}

/**
 * Remplace le fichier sélectionné par le nouveau fichier téléversé
 * @param file
 */
function majPhoto(file) {
    // l'identifiant est mémorisé car idEtudiant peut changer pendant l'appel AJAX
    const id = idEtudiant;

    // Création d'un objet FormData pour envoyer le fichier et l'identifiant de l'étudiant.
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('primaryKey', id);

    // Appel AJAX pour mettre à jour la photo.
    appelAjax({
        url: 'ajax/majphoto.php',
        data: formData,
        success: (reponse) => {
            // la réponse contient le nom donné à la photo par le serveur
            const cible = document.getElementById(`cible${id}`);
            const image = cible.querySelector('.carte-img');
            const zoneVide = cible.querySelector('.photo-vide');
            const boutonSupprimer = cible.querySelector('.btn-supprimer-photo');

            // le paramètre t force le navigateur à recharger l'image (le nom ne change pas lors d'un remplacement)
            image.src = `${repertoire}/${encodeURIComponent(reponse.photo)}?t=${Date.now()}`;
            image.hidden = false;
            zoneVide.hidden = true;
            boutonSupprimer.hidden = false;

            afficherToast("La photo a été mise à jour");
        }
    });
}

/**
 * Supprime la photo de l'étudiant et affiche à nouveau la photo par défaut.
 */
function supprimerPhoto(id, image, zoneVide, bouton) {
    appelAjax({
        url: 'ajax/supprimerphoto.php',
        data: {primaryKey: id},
        success: (reponse) => {
            // retour à l'état "sans photo"
            image.removeAttribute('src');
            image.hidden = true;
            zoneVide.hidden = false;
            bouton.hidden = true;

            // message renvoyé par le serveur (3 variantes possibles)
            afficherToast(reponse.message, 'success', 'bottom-center', 3000);
        }
    });
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Génération des cartes à partir des données
const fragment = document.createDocumentFragment();
for (const element of lesEtudiants) {
    fragment.appendChild(creerCarte(element));
}
lesCartes.appendChild(fragment);