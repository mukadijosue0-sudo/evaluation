"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {afficherToast, messageBox} from '/composant/fonction/afficher.js';
import {
    configurerFormulaire,
    donneesValides,
    configurerDate,
    filtrerLaSaisie,
    effacerLesErreurs
} from "/composant/fonction/formulaire.js";
import {ucWord} from "/composant/fonction/chaine.js";
import {getData} from "/composant/fonction/page.js";
import {getDateRelative} from '/composant/fonction/date.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEtudiants = getData("lesEtudiants");

// Récupération des éléments de l'interface

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');

const search = document.getElementById('search');
const btnModifier = document.getElementById('btnModifier');
const msg = document.getElementById('msg');
const zoneSaisie = document.getElementById('zoneSaisie');

let etudiant = {};

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.onfocus = () => {
    search.value = '';
    zoneSaisie.style.visibility = 'hidden';
};

/**

 Procédure évènementielle sur le bouton modifier
 */
btnModifier.onclick = () => {
    effacerLesErreurs();

// Normalisation des valeurs
    nom.value = nom.value.trim().toLocaleUpperCase('fr-FR');
    prenom.value = ucWord(prenom.value.trim());

// Nouvelles valeurs à enregistrer
    const columns = {
        nom: nom.value,
        prenom: prenom.value,
        sexe: sexe.value,
        dateNaissance: dateNaissance.value
    };

// Recherche des colonnes réellement modifiées
    const modifiees = Object.keys(columns).filter(
        colonne => columns[colonne] !== etudiant[colonne]
    );

// Aucune modification
    if (modifiees.length === 0) {
        messageBox("Aucune modification constatée", 'info');
        return;
    }

// Validation puis enregistrement
    if (donneesValides()) {
        modifier(columns, modifiees);
    }
};

// Transformer immédiatement le nom en majuscule
nom.addEventListener('input', () => {
    const pos = nom.selectionStart;

    nom.value = nom.value.toLocaleUpperCase('fr-FR');

    nom.setSelectionRange(pos, pos);

});

// Transformer le prénom en casse appropriée
prenom.addEventListener('input', () => {
    const pos = prenom.selectionStart;
    const dernierCaractere = prenom.value[pos - 1];

// Ne rien faire si le dernier caractère est un espace,
// une apostrophe ou un tiret
    if ([' ', '\'', '-'].includes(dernierCaractere)) {
        return;
    }

    prenom.value = ucWord(prenom.value);
    prenom.setSelectionRange(pos, pos);

});

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**

 Affiche les données de l'étudiant sélectionné

 et les conserve dans l'objet etudiant afin de détecter

 les modifications ultérieures.

 @param {Object} data
 */
function afficher(data) {
// Sauvegarde des données d'origine
    etudiant = data;

// Affichage des données
    nom.value = etudiant.nom;
    prenom.value = etudiant.prenom;
    sexe.value = etudiant.sexe;
    dateNaissance.value = etudiant.dateNaissance;

    search.blur();

// Suppression de la surbrillance précédente
    for (const champ of [nom, prenom, sexe, dateNaissance]) {
        champ.style.color = '';
        champ.style.border = '';
    }

// Affichage du formulaire
    zoneSaisie.style.visibility = 'visible';
}

/**

 Enregistre les modifications de l'étudiant.

 @param {Object} columns valeurs à enregistrer

 @param {string[]} modifiees noms des colonnes modifiées
 */
function modifier(columns, modifiees) {
    msg.innerHTML = '';

    appelAjax({
        url: 'ajax/modifier.php',

        data: {
            primaryKey: etudiant.id,
            columns: columns
        },

        success: () => {

            // Correspondance entre le nom de la colonne
            // et l'élément HTML correspondant
            const champs = {
                nom,
                prenom,
                sexe,
                dateNaissance
            };

            // Mise en surbrillance des champs modifiés
            for (const colonne of modifiees) {
                champs[colonne].style.border = '2px solid green';
                champs[colonne].style.color = 'green';
            }

            // Mise à jour des données locales
            Object.assign(etudiant, columns);

            // Mise à jour du champ de recherche
            const nomComplet = `${etudiant.nom} ${etudiant.prenom}`;
            search.value = nomComplet;

            // Mise à jour du tableau d'autocomplétion
            const index = lesEtudiants.findIndex(
                e => e.id === etudiant.id
            );

            if (index !== -1) {
                lesEtudiants[index].nom = etudiant.nom;
                lesEtudiants[index].prenom = etudiant.prenom;
                lesEtudiants[index].nomPrenom = nomComplet;
            }

            afficherToast('Étudiant modifié', 'success');
        }

    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Initialisation du composant autoComplete.js
const autoCompleteJS = new autoComplete({
    selector: "#search",
    threshold: 1,
    debounce: 300,

    data: {
        src: lesEtudiants,
        cache: true,
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
                afficher(selection);
            }
        }
    }

});

// Définition des bornes de la date de naissance
// entre 17 et 25 ans
configurerDate(dateNaissance, {
    min: getDateRelative('annee', -25),
    max: getDateRelative('annee', -17),
    valeur: getDateRelative('annee', -19),
});

// Contrôle des données saisies après le système d'autocomplétion
configurerFormulaire();

filtrerLaSaisie(
    'nom',
    /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/
);

filtrerLaSaisie(
    'search',
    /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/
);

filtrerLaSaisie(
    'prenom',
    /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/
);