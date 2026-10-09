"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getAge} from '/composant/fonction/date.js';
import {effacerSousLeChamp} from "/composant/fonction/afficher.js";
import {appelAjax} from "/composant/fonction/ajax.js";

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

function rechercher(id) {
    appelAjax({
        url: 'ajax/getbyname.php',
        data: {
            id: id
        },
        success: afficher
    });
}

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
        src: async (query) => {
            const reponse = await appelAjax({
                url: "ajax/getbyname.php?search=" + query,
                method: "GET",
                dataType: "json"
            });

            console.log("Résultat de la recherche :", reponse);

            return reponse ?? [];
        },

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

                message.textContent =
                    "Aucun étudiant trouvé." +
                    (data.query ? ` pour "${data.query}"` : "");

                message.setAttribute("class", "no_result");

                list.appendChild(message);
            }
        }
    },

    resultItem: {
        highlight: true,

        element: (item, data) => {
            item.innerHTML = `<span>${data.match}</span>`;
        }
    },

    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;

                search.value = selection.nomPrenom;

                afficher(selection);
            }
        }
    }
});