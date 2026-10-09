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
    effacerLesErreurs,
    effacerLesChamps
} from "/composant/fonction/formulaire.js";
import {ucWord} from "/composant/fonction/chaine.js";
import {getData} from "/composant/fonction/page.js";
import {getDateRelative} from '/composant/fonction/date.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------


const lesEtudiants = getData("lesEtudiants");


// récupération des éléments de l'interface

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');

const search= document.getElementById('search');
const btnModifier = document.getElementById('btnModifier');
const msg = document.getElementById('msg');
const zoneSaisie = document.getElementById('zoneSaisie');

let etudiant = {}; // objet global contenant les informations sur l'étudiant sélectionné

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

search.onfocus = () => {
    search.value = '';
    zoneSaisie.style.visibility = 'hidden';

};


/**
 * Procédure évènementielle sur le bouton modifier
 */
btnModifier.onclick = () => {
    effacerLesErreurs();

    // 1. Contrôle préalable des modifications
    if (!estModifie()) {
        messageBox("Aucune modification constatée", 'info');
        return;
    }

    // 2. Normalisation et validation
    nom.value = nom.value.trim().toLocaleUpperCase('fr-FR');
    prenom.value = ucWord(prenom.value.trim());

    if (donneesValides()) {
        modifier();
    }
};


// Transformer immédiatement le nom en majuscule
nom.addEventListener('input', () => {
    const pos = nom.selectionStart;
    nom.value = nom.value.toLocaleUpperCase('fr-FR');
    nom.setSelectionRange(pos, pos);
});

prenom.addEventListener('input', () => {
    const pos = prenom.selectionStart;
    const dernierCaractere = prenom.value[pos - 1];

    // Ne rien faire si le dernier caractère est un espace, apostrophe ou tiret
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
 * affichage des coordonnées du coureur contenues dans le paramètre implicite data
 * On conserve les coordonnées du coureur dans l'objet coureur afin de détecter une modification
 * @param data
 */
function afficher(data) {
    // sauvegarde des données dans l'objet global etudiant afin de détecter les modifications
    etudiant = data;

    // affichage des données
    nom.value = etudiant.nom;
    prenom.value = etudiant.prenom;
    sexe.value = etudiant.sexe;
    dateNaissance.value = etudiant.dateNaissance;
    search.blur(); // retire le focus du champ de saisie

    // afficher le formulaire
    for (const champ of [nom, prenom, sexe, dateNaissance]) {
        champ.style.color = '';
        champ.style.border = '';
    }
    zoneSaisie.style.visibility = 'visible';
}

function estModifie() {
    return nom.value.trim() !== etudiant.nom
        || ucWord(prenom.value.trim()) !== etudiant.prenom
        || sexe.value !== etudiant.sexe
        || dateNaissance.value !== etudiant.dateNaissance;
}

function modifier() {
    msg.innerHTML = '';

    const columns = {
        nom: nom.value,
        prenom: prenom.value,
        sexe: sexe.value,
        dateNaissance: dateNaissance.value
    };

    appelAjax({
        url: 'ajax/modifier.php',
        data: {
            primaryKey: etudiant.id,
            columns: columns
        },
        success: () => {
            // Mettre en surbrillance les champs effectivement modifiés
            for (const cle in columns) {
                if (columns[cle] !== etudiant[cle]) {
                    const champ = document.getElementById(cle);
                    if (champ) {
                        champ.style.border = '2px solid green';
                        champ.style.color = 'green';
                    }
                }
            }

            // Mise à jour de l'objet local
            Object.assign(etudiant, columns);

            // Mise à jour du champ de recherche et du tableau d'autocomplétion
            const nomComplet = `${etudiant.nom} ${etudiant.prenom}`;
            search.value = nomComplet;

            const index = lesEtudiants.findIndex(e => e.id === etudiant.id);
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

// initialisation du composant autoComplete.js
const autoCompleteJS = new autoComplete({
    selector: "#search",
    threshold: 1,
    debounce: 300,
    data: {
        src: lesEtudiants,
        cache : true,
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

// définition des bornes de la date de naissance (entre 17 et 25 ans)
configurerDate(dateNaissance, {
    min:getDateRelative('annee', -25),
    max: getDateRelative('annee', -17),
    valeur: getDateRelative('annee', -19),
});

// contrôle des données saisies toujours après le système d'autocomplétion qui vient ajouter ses propres balises
configurerFormulaire();
filtrerLaSaisie('nom', /[A-Za-z '-]/);
filtrerLaSaisie('search',  /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);
filtrerLaSaisie('prenom', /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);
