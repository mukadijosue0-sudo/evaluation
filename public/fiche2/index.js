"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import { initAutoComplete } from "/composant/fonction/autocomplete.js";
import { getAge } from "/composant/fonction/date.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const search = document.getElementById('search');

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const age = document.getElementById('age');
const libelleCourt = document.getElementById('libelleCourt');
const photo = document.getElementById('photo');

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Alimente l'interface avec les données de l'étudiant sélectionné
 * @param {Object} etudiant
 */
function afficher(etudiant) {
    nom.textContent = etudiant.nom;
    prenom.textContent = etudiant.prenom;
    sexe.textContent = etudiant.sexe;
    dateNaissance.textContent = etudiant.dateNaissanceFr;
    age.textContent = getAge(etudiant.dateNaissanceFr) + ' ans';
    libelleCourt.textContent = etudiant.libelleCourt;

    // la photo est remplacée par une image par défaut si le fichier est absent
    photo.innerHTML = '';
    const img = document.createElement('img');
    img.src = '/data/photo/' + (etudiant.present ? etudiant.photo : '0.png');
    img.alt = etudiant.nomPrenom;
    img.style.maxHeight = '100%';
    img.style.maxWidth = '100%';
    photo.appendChild(img);
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

initAutoComplete({
    selector: "#search",
    fetchUrl: "/fiche2/ajax/getbyname.php",
    searchKey: "nomPrenom",
    onSelection: (etudiant) => {
        // etudiant contient déjà toutes les données : aucun second appel AJAX
        search.value = etudiant.nomPrenom;
        afficher(etudiant);
    }
});