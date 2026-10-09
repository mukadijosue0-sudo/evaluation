<?php
declare(strict_types=1);

return [

    // Répertoire de destination des fichiers uploadés
    'repertoire' => DOSSIER_PHOTO_ETUDIANT,

    // Taille maximale (150 Ko)
    'maxSize' => 150 * 1024,

    // Dimensions maximales
    'maxWidth' => 150,
    'maxHeight' => 150,

    // L'image doit-elle être redimensionnée si elle dépasse la largeur ou la hauteur maximale.
    'resized' => true,

    // Extensions autorisées
    'lesExtensions' => ['jpg', 'png'],

    // Types MIME autorisés
    'lesTypes' => ['image/jpeg', 'image/png'],

    // Si le fichier existe déjà, doit-il être renommé ? (non : doublon refusé)
    'renommerSiExiste' => false,
];