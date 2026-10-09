"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------


// Récupération des données depuis le serveur


// Extraction des paramètres


// Récupération des éléments de l'interface
const fichier = document.getElementById("fichier");
const lesCartes = document.getElementById("lesCartes");
const tplCarte = document.getElementById("tplCarteEtudiant");

// Identifiant de l'étudiant dont la photo doit être remplacée


// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// Lancer la fonction controlerFichier si un fichier a été sélectionné dans l'explorateur


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

    // Vérification de taille et d'extension

    // Vérifications spécifiques pour un fichier image
    // la fonction de rappel reçoit le fichier et l'image éventuellement redimensionnée si le redimensionnement est demandé

}

/**
 * Remplace le fichier sélectionné par le nouveau fichier téléversé
 * @param file
 */
function majPhoto(file) {
    // Création d'un objet FormData pour envoyer le fichier et l'identifiant de l'étudiant.

    // Appel AJAX pour mettre à jour la photo.

}

/**
 * Supprime la photo de l'étudiant et affiche à nouveau la photo par défaut.
 */
function supprimerPhoto(id, img, bouton) {

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
