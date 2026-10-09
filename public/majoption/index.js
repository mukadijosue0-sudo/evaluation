"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from '/composant/fonction/ajax.js';
import {afficherToast} from '/composant/fonction/afficher.js';
import {creerTr, creerTd, creerSelect} from "/composant/fonction/dom.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------


const lesEtudiants = getData("lesEtudiants");
const lesOptions = getData("lesOptions");

const lesLignes = document.getElementById('lesLignes');
const msg = document.getElementById('msg');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------


/**
 * Crée et retourne une ligne de tableau représentant une etudiant.
 * @param {object} etudiant - Objet etudiant avec id, dateFr, nom, actif
 * @returns {HTMLTableRowElement}
 */
function creerLigneetudiant(etudiant) {

    // 1. Colonne nom prénom
    const tdNomPrenom = creerTd(etudiant.nomPrenom);

    // 2. Colonne option sous la forme d'une zone de liste déroulante
    const idOption = creerSelect();
    // alimentation de la liste des options
    for (const element of lesOptions) {
        idOption.add(new Option(element.libelleLong, element.id));
    }
    // sélection de l'option de l'étudiant
    idOption.value = etudiant.idOption;

    //
    idOption.onchange = () => {
        appelAjax({
            url: 'ajax/modifieroption.php',
            data: {
                primarykey: etudiant.id,
                columns: {idOption: idOption.value}
            },
            success: (data) => {
                afficherToast(data.message);
            }
        });
    };

    // création de la cellule contenant la liste déroulante
    const tdIdOption = creerTd('');
    tdIdOption.appendChild(idOption);

    // Création de la ligne
    const tr = creerTr([tdNomPrenom, tdIdOption]);
    tr.id = etudiant.id;

    return tr;
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// génération du tableau des étudiants avec leurs options
lesLignes.innerHTML = '';
for (const etudiant of lesEtudiants) {
    const ligne = creerLigneetudiant(etudiant);
    lesLignes.appendChild(ligne);
}
