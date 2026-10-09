"use strict";
// Version 2026.1
// Date version : 08/08/2026

/**
 * Crée une cellule de tableau (<td>) avec du texte ou du HTML.
 *
 * @param {string} contenu - Texte ou HTML à insérer.
 * @param {Object} [options] - Options de style.
 * @param {boolean} [options.centrer=false] - Centre le contenu horizontalement.
 * @param {boolean} [options.masquer=false] - Ajoute la classe "masquer".
 * @param {boolean} [options.isHTML=false] - Interprète le contenu comme du HTML.
 * @returns {HTMLTableCellElement}
 */
export function creerTd(contenu, {centrer = false, masquer = false, isHTML = false} = {}) {
    const td = document.createElement('td');

    if (isHTML) {
        td.innerHTML = contenu;
    } else {
        td.innerText = contenu;
    }

    if (centrer) {
        td.style.textAlign = 'center';
    }

    if (masquer) {
        td.classList.add('masquer');
    }
    return td;
}

/**
 * Crée une cellule de tableau (<td>) contenant une image.
 *
 * @param {string} src - Chemin ou URL de l'image.
 * @param {string} [alt=''] - Texte alternatif de l'image.
 * @param {Object} [options] - Options d'affichage de l'image.
 * @param {number} [options.size=40] - Taille de l'image en pixels (largeur et hauteur).
 * @param {string} [options.radius='50%'] - Rayon des bordures de l'image.
 * @param {boolean} [options.masquer=false] - Ajoute la classe "masquer" à la cellule.
 * @returns {HTMLTableCellElement}
 */
export function creerTdWithImg(src, alt = '', {size = 40, radius = '50%', masquer = false} = {}) {
    const td = document.createElement('td');
    const img = document.createElement('img');

    img.src = src;
    img.alt = alt;
    img.style.width = img.style.height = `${size}px`;
    img.style.borderRadius = radius;
    img.style.objectFit = 'cover';

    td.appendChild(img);

    if (masquer) {
        td.classList.add('masquer');
    }

    return td;
}

/**
 * Construit une ligne de tableau (<tr>) à partir d'un tableau de cellules.
 *
 * @param {HTMLTableCellElement[]} [lesTds=[]] - Cellules à insérer dans la ligne.
 * @returns {HTMLTableRowElement}
 */
export function creerTr(lesTds = []) {
    const tr = document.createElement('tr');
    tr.style.verticalAlign = 'middle';

    for (const td of lesTds) {
        tr.appendChild(td);
    }
    return tr;
}
