"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEtudiants = getData('lesEtudiants');

const lesCartes = document.getElementById('lesCartes');
const tplCarte = document.getElementById('tplCarteEtudiant');
const btnPdf = document.getElementById('btnPdf');

// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------
btnPdf.addEventListener("click", () => {
    const element = document.getElementById("pdfContent");

    const opt = {
        margin:       0.5,
        filename:     'etudiants.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 1 },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
    };

    html2pdf().set(opt).from(element).save();
});


// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Crée une carte pour afficher les informations d'un étudiant à partir du template HTML.
 * @param {Object} element - Données de l'étudiant
 * @returns {DocumentFragment}
 */
function creerCarte(element) {
    // Clonage du contenu du template
    const clone = tplCarte.content.cloneNode(true);

    // Injection du nom/prénom et du pied
    clone.querySelector('.carte-header').innerHTML = element.nom + '<br>' + element.prenom;
    clone.querySelector('.carte-footer').textContent = element.libelleCourt;

    // Gestion de l'image
    const img = clone.querySelector('.carte-img');

    if (element.present) {
        img.src = `/data/photo/${element.photo}`;
        img.alt = `Photo de ${element.nomPrenom}`;
    } else {
        img.src = '/data/photo/0.png';
        img.alt = 'Photo par défaut';
    }

    return clone;
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Utilisation d'un DocumentFragment pour minimiser les redessins du DOM (reflows)
const fragment = document.createDocumentFragment();

for (const element of lesEtudiants) {
    fragment.appendChild(creerCarte(element));
}

// Un seul insert dans le DOM
lesCartes.appendChild(fragment);