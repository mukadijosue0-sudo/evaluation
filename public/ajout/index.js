"use strict";
// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {genererMessage, retournerVers} from "/composant/fonction/afficher.js";
import {configurerFormulaire, configurerDate, donneesValides, filtrerLaSaisie } from "/composant/fonction/formulaire.js";
import {enleverAccent, supprimerEspace, ucFirst} from '/composant/fonction/chaine.js';
import {getData} from "/composant/fonction/page.js";
import {getDateRelative} from '/composant/fonction/date.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesOptions = getData("lesOptions");
const lesEtudiants = getData("lesEtudiants");

const nom = document.getElementById('nom');
const prenom = document.getElementById('prenom');
const sexe = document.getElementById('sexe');
const dateNaissance = document.getElementById('dateNaissance');
const idOption = document.getElementById('idOption');
const msg = document.getElementById('msg');
const btnAjouter = document.getElementById('btnAjouter');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

btnAjouter.onclick = () => {
    // mise en forme des données
    nom.value = enleverAccent(supprimerEspace(nom.value)).toUpperCase();
    prenom.value = ucFirst(supprimerEspace(prenom.value).toUpperCase());
    // contrôle des champs de saisie
    msg.innerHTML = "";
    if (lesEtudiants.some(c => c.nom === nom.value && c.prenom === prenom.value)) {
        msg.innerHTML = genererMessage("Un étudiant avec ce nom et ce prénom est déjà enregistré.");
        return;
    }

    if (donneesValides()) {
        ajouter();
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function ajouter() {
    appelAjax({
        url: 'ajax/ajouter.php',
        data: {
            columns: {
                nom: nom.value,
                prenom: prenom.value,
                sexe: sexe.value,
                dateNaissance: dateNaissance.value,
                idOption: idOption.value,
            }
        },
        success: (reponse) => retournerVers(reponse.message, "/liste")
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// alimentation de la zone de liste des clubs
for (const element of lesOptions) {
    idOption.add(new Option(element.libelleLong, element.id));
}

// Selection de l'option par défaut' TC
idOption.selectedIndex = 2;

// contrôle des données
configurerFormulaire();
filtrerLaSaisie('nom', /[A-Za-z '-]/);
filtrerLaSaisie('prenom', /[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]/);


// définition des bornes de la date de naissance (entre 17 et 25 ans)
configurerDate(dateNaissance, {
    min:getDateRelative('annee', -25),
    max: getDateRelative('annee', -17),
    valeur: getDateRelative('annee', -19),
});

// Données de test
nom.value = 'Dûpont';
prenom.value = 'Hervé';

