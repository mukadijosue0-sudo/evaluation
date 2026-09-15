"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {ucWord} from "/composant/fonction/chaine.js";
import {activerTri} from "/composant/fonction/tableau.js";
import {getData} from "/composant/fonction/page.js";
import {creerTd, creerTdWithImg, creerTr} from '/composant/fonction/trtd.js';

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEtudiants = getData('lesEtudiants');

const lesLignes = document.getElementById('lesLignes');

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Affichage des étudiants dans le tableau
 * @param lesEtudiants
 */
function afficher2(lesEtudiants) {
    // effacer le tableau précédent
    lesLignes.innerHTML = "";
    for (const etudiant of lesEtudiants) {
        const tr = lesLignes.insertRow();
        tr.style.verticalAlign = 'middle';
        tr.setAttribute('tabindex', '0'); // Permet l'activation au clic/toucher tactile

        const nomFormate = ucWord(etudiant.nomPrenom);

        // Alimentation du détail pour l'affichage mobile
        const details =
            `👫 Sexe : ${etudiant.sexe}\n` +
            `🎂 Né(e) le : ${etudiant.dateNaissanceFr}`;

        // Colonne 1 : nomPrenom (On place data-details directement sur cette cellule)
        const tdNom = tr.insertCell();
        tdNom.innerText = nomFormate;
        tdNom.setAttribute('data-details', details);

        // Colonne 2 : Sexe
        let td = tr.insertCell();
        td.innerText = etudiant.sexe;
        td.style.textAlign = 'center';
        td.classList.add('col-sexe');

        // Colonne 3 : Âge/Naissance
        td = tr.insertCell();
        td.innerText = etudiant.dateNaissanceFr;
        td.style.textAlign = 'center';
        td.classList.add('col-naissance');

        // Colonne 4 : Option
        td = tr.insertCell();
        td.innerText = etudiant.libelleCourt;
        td.style.textAlign = 'center';

        // Colonne 5 : Photo
        td = tr.insertCell();
        const photo = etudiant.present ? etudiant.photo : '0.png';
        const img = document.createElement('img');
        img.src = '/data/photo/' + photo;
        img.alt = nomFormate;
        td.appendChild(img);
        td.style.textAlign = 'center';
    }
}


function afficher(lesEtudiants) {
    // Effacer le tableau précédent
    lesLignes.innerHTML = "";

    for (const etudiant of lesEtudiants) {
        const nomFormate = ucWord(etudiant.nomPrenom);

        // Alimentation du détail pour l'affichage mobile
        const details =
            `👫 Sexe : ${etudiant.sexe}\n` +
            `🎂 Né(e) le : ${etudiant.dateNaissanceFr}`;

        // Preparation des cellules (td)
        const tdNom = creerTd(nomFormate);
        tdNom.setAttribute('data-details', details);

        const tdSexe = creerTd(etudiant.sexe, {centrer: true});
        tdSexe.classList.add('col-sexe');

        const tdNaissance = creerTd(etudiant.dateNaissanceFr, {centrer: true});
        tdNaissance.classList.add('col-naissance');

        const tdOption = creerTd(etudiant.libelleCourt, {centrer: true});

        const photo = etudiant.present ? etudiant.photo : '0.png';
        const tdPhoto = creerTdWithImg('/data/photo/' + photo, nomFormate, {centrer: true});

        // Regroupement des cellules
        const lesTds = [tdNom, tdSexe, tdNaissance, tdOption, tdPhoto];

        // Création de la ligne et ajout des attributs spécifiques au TR
        const tr = creerTr(lesTds);
        tr.setAttribute('tabindex', '0'); // Permet l'activation au clic/toucher tactile

        // Ajout dans le tbody
        lesLignes.appendChild(tr);
    }
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

afficher(lesEtudiants);

// Activer le tri sur les colonnes
activerTri({
    idTable: "leTableau",
    getData: () => lesEtudiants,
    afficher: afficher,
    triInitial: {
        colonne: 'nomPrenom',
        ordre: "asc"
    }
});