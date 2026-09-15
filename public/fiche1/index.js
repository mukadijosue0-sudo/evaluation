"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getAge} from '/composant/fonction/date.js';
import {effacerSousLeChamp} from "/composant/fonction/afficher.js";
import {appelAjax} from "/composant/fonction/ajax.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// liste légère (id + nomPrenom) déjà en mémoire, fournie par le contrôleur
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
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.onfocus = function() {
    effacerSousLeChamp('search');
};

search.onblur = function() {
    effacerSousLeChamp('search');
};

search.oninput = function() {
    effacerSousLeChamp('search');
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher(etudiant) {
    nom.textContent = etudiant.nom;
    prenom.textContent = etudiant.prenom;
    sexe.textContent = etudiant.sexe;
    dateNaissance.textContent = etudiant.dateNaissanceFr;
    age.textContent = getAge(etudiant.dateNaissanceFr) + ' ans';
    libelleCourt.textContent = etudiant.libelleCourt;

    photo.innerHTML = '';
    const img = document.createElement('img');
    img.src = '/data/photo/' + (etudiant.present ? etudiant.photo : '0.png');
    img.alt = etudiant.nomPrenom;
    photo.appendChild(img);

    search.blur();
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

const autoCompleteJS = new autoComplete({
    selector: "#search",
    threshold: 1,
    debounce: 300,
    data: {
        // pas d'appel AJAX : la recherche filtre le tableau déjà en mémoire
        src: lesEtudiants,
        keys: ["nomPrenom"],
        cache: false
    },
    searchEngine: "loose",
    resultsList: {
        maxResults: 10,
        noResults: true,
        element: (list, data) => {
            if (!data.results.length) {
                const message = document.createElement("li");
                message.textContent = "Aucun étudiant trouvé." + (data.query ? ` pour "${data.query}"` : "");
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
            selection: async (event) => {
                const selection = event.detail.selection.value;
                search.value = selection.nomPrenom;

                // sélection faite : appel AJAX pour récupérer les données complètes
                const etudiant = await appelAjax({
                    url: "ajax/getbyid.php?id=" + selection.id,
                    method: "GET",
                    dataType: "json"
                });

                afficher(etudiant);
            }
        }
    }
});