"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from '/composant/fonction/ajax.js';
import {afficherToast, confirmer} from '/composant/fonction/afficher.js';
import {getData} from '/composant/fonction/page.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEtudiants = getData('lesEtudiants');
const lesCartes = document.getElementById('lesCartes');
const tplCarte = document.getElementById('tplCarteEtudiant');
const repertoirePhoto = '/data/photo';

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------
/**
 * Supprime l'étudiant après confirmation et retire sa carte si la requête réussit.
 *
 * @param {number} id - L'identifiant de l'étudiant à supprimer.
 */
function supprimerEtudiant(id) {
    appelAjax({
        url: 'ajax/supprimer.php',
        data: {primaryKey: id},
        success: () => {
            document.getElementById(`etudiant${id}`)?.remove();
            afficherToast("L'étudiant a été supprimé");
        }
    });
}

/**
 * Crée une carte étudiant à partir du template HTML.
 * @param {Object} etudiant - Les données de l'étudiant.
 * @param {string} etudiant.id - L'identifiant de l'étudiant.
 * @param {string} etudiant.nomPrenom - Le nom et prénom de l'étudiant.
 * @param {boolean} etudiant.present - Indique si l'étudiant est présent.
 * @param {string} etudiant.photo - Le nom du fichier photo de l'étudiant.
 * @returns {HTMLElement} - La carte étudiant créée.
 */
function creerCarte(etudiant) {
    const carte = tplCarte.content.firstElementChild.cloneNode(true);
    const image = carte.querySelector('.carte-img');
    const bouton = carte.querySelector('.btn-supprimer');

    // Définir l'ID de la carte pour pouvoir la retrouver facilement
    carte.id = `etudiant${etudiant.id}`;
    carte.querySelector('.nom').textContent = etudiant.nom;
    carte.querySelector('.prenom').textContent = etudiant.prenom;

    image.alt = etudiant.present
        ? `Photo de ${etudiant.nomPrenom}`
        : 'Photo par défaut';
    image.src = etudiant.present
        ? `${repertoirePhoto}/${encodeURIComponent(etudiant.photo)}?t=${Date.now()}`
        : `${repertoirePhoto}/0.png`;

    bouton.setAttribute('aria-label', `Supprimer l'étudiant ${etudiant.nomPrenom}`);
    // Ajout de l'évènement click sur le bouton de suppression pour déclencher la suppression après confirmation
    bouton.addEventListener('click', () => {
        confirmer(  () => supprimerEtudiant(etudiant.id),  `Voulez-vous vraiment supprimer l'étudiant ${etudiant.nomPrenom} ?`);
    });

    return carte;
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Création d'un fragment pour améliorer les performances lors de l'ajout des cartes au DOM
const fragment = document.createDocumentFragment();
for (const etudiant of lesEtudiants) {
    fragment.appendChild(creerCarte(etudiant));
}
lesCartes.appendChild(fragment);
