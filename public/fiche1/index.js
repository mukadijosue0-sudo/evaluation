"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getAge} from '/composant/fonction/date.js';
import {effacerSousLeChamp} from "/composant/fonction/afficher.js";
import {getData} from "/composant/fonction/page.js";
import {appelAjax} from "/composant/fonction/ajax.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Données injectées par le contrôleur PHP
const lesEtudiants = getData('lesEtudiants');

const search = document.getElementById('search');
const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const age = document.getElementById('age');
const libelleCourt = document.getElementById('libelleCourt');
const photo = document.getElementById('photo');

// -----------------------------------------------------------------------------------
// Procédures événementielles
// -----------------------------------------------------------------------------------

search.onfocus = function () {
    // Efface les valeurs de la fiche
    document.querySelectorAll('output').forEach(element => element.textContent = '');
    // Efface le champ de recherche
    this.value = '';
    // Efface un éventuel message d'erreur
    effacerSousLeChamp('search');
};

search.onblur = function () {
    effacerSousLeChamp('search');
};

search.oninput = function () {
    effacerSousLeChamp('search');
};


// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

// Recherche des données de l'étudiant
function rechercher(id) {

    appelAjax({
        url: 'ajax/getbyid.php',
        data: {
            id: id
        },
        success: afficher
    });
}

// Affichage des données de l'étudiant
function afficher(etudiant) {
    nom.textContent = etudiant.nom;
    prenom.textContent = etudiant.prenom;
    sexe.textContent = etudiant.sexe;
    dateNaissance.textContent = etudiant.dateNaissanceFr;
    age.textContent = getAge(etudiant.dateNaissanceFr) + ' ans';
    libelleCourt.textContent = etudiant.libelleCourt;

    // Gestion de la photo
    const img = document.createElement('img');

    if (etudiant.present && etudiant.photo) {
        img.src = '/data/photo/' + etudiant.photo;
        img.alt = `Photo de ${etudiant.prenom} ${etudiant.nom}`;
    } else {
        img.src = '/data/photo/0.png';
        img.alt = 'Photo par défaut';
    }

    photo.textContent = ''; // Réinitialise l'affichage précédant
    photo.appendChild(img);

    search.blur(); // Retire le focus du champ de saisie
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// initialisation du composant autoComplete.js
const autoCompleteJS = new autoComplete({
    selector: "#search",
    threshold: 1,
    debounce: 300,
    data: {
        src: lesEtudiants,
        cache: false,
        keys: ["nomPrenom"],
    },
    searchEngine: "loose",
    resultsList: {
        maxResults: 10,
        noResults: true,
        element: (list, data) => {
            if (!data.results.length) {
                const message = document.createElement("li");

                message.textContent = "Aucun étudiant trouvé.";
                message.setAttribute("class", "no_result");

                list.appendChild(message);
            }
        }
    },
    resultItem: {
        highlight: true,
        element: (item, data) => item.innerHTML = `<span>${data.match}</span>`
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                search.value = selection.nomPrenom;
                rechercher(selection.id);
            }
        }
    }
});