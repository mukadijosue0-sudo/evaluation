// noinspection JSUnusedGlobalSymbols

// Active le mode strict afin d'éviter les erreurs silencieuses et d'imposer un JS plus rigoureux.
'use strict';

// ============================================================================
// Version      : 2026.2
// Date         : 11/08/2026
// ============================================================================

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
export function creerTdAvecImage(src, alt = '', {size = 40, radius = '50%', masquer = false} = {}) {
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



/**
 * Crée dynamiquement un bouton d'action (icône cliquable)
 *
 * @param {Object} options - Paramètres de configuration du bouton
 * @param {string} options.icone - Symbole ou texte affiché (ex : ✎, ✘)
 * @param {string} [options.couleur='black'] - Couleur du symbole
 * @param {string} [options.titre=''] - Info-bulle (attribut title)
 * @param {function} [options.action] - Fonction de rappel exécutée au clic
 * @returns {HTMLElement} Élément <span> configuré
 */
export function creerBoutonAction({
                                      icone,
                                      couleur = 'black',
                                      titre = '',
                                      action = null
                                  }) {
    const bouton = document.createElement('button');
    bouton.textContent = icone;
    bouton.title = titre;

    // Style du bouton pour ressembler à un lien hypertexte
    Object.assign(bouton.style, {
        color: couleur,
        background: 'none',
        border: 'none',
        padding: '0',
        margin: '0',
        fontSize: '1em',
        userSelect: 'none',
        cursor: 'pointer',
        transition: 'transform 0.2s ease, box-shadow 0.2s ease',
        verticalAlign: 'middle',
        display: 'inline' // Pour éviter le saut de ligne
    });

    // Ajouter une classe pour cibler le hover en JS
    bouton.classList.add('bouton-action');
    // Ajouter dynamiquement la règle CSS hover si elle n'existe pas déjà
    injecterHoverStyleBouton();
    if (typeof action === 'function') {
        bouton.addEventListener('click', action);
    }
    return bouton;
}

function injecterHoverStyleBouton() {
    const styleId = 'style-bouton-action-hover';
    if (document.getElementById(styleId)) {
        return;
    }

    const style = document.createElement('style');
    style.id = styleId;
    style.textContent = `
        .bouton-action:hover {
            transform: scale(1.3);
        }
    `;
    document.head.appendChild(style);
}


export function creerBoutonModification(action) {
    return creerBoutonAction({
        icone: '✎',
        couleur: 'orange',
        titre: 'Modifier l\'enregistrement',
        action: action
    });
}


export function creerBoutonSuppression(action) {
    return creerBoutonAction({
        icone: '✘',
        couleur: 'red',
        titre: 'Supprimer l\'enregistrement',
        action: action
    });
}

export function creerBoutonRemplacer(action) {
    return creerBoutonAction({
        icone: '♻️',
        couleur: 'red',
        titre: 'Téléverser une nouvelle version du document PDF',
        action: action
    });
}

export function creerCheckbox() {
    const input = document.createElement('input');
    input.type = 'checkbox';
    input.classList.add('form-check-input', 'my-auto', 'm-3');
    input.style.width = '25px';
    input.style.height = '25px';
    return input;
}

/**
 * Crée un élément <select> stylisé.
 * @param {Array<string>} [classes=[]] - Liste de classes CSS à appliquer.
 * @returns {HTMLSelectElement}
 */
export function creerSelect(classes = []) {
    const select = document.createElement('select');
    select.classList.add(...classes);
    return select;
}

/**
 * Crée un champ <input type="text"> stylisé.
 * @param {Object} [options={}]
 * @param {string} [options.placeholder] - Texte d’aide visuel.
 * @param {Array<string>} [options.classes] - Classes CSS à appliquer.
 * @returns {HTMLInputElement}
 */
export function creerInputTexte({placeholder = '', classes = []} = {}) {
    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = placeholder;
    input.classList.add(...classes);
    return input;
}


