<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Contraintes SQL de l'application
|--------------------------------------------------------------------------
|
| Ce fichier associe le nom d'une contrainte SQL à un message destiné
| à l'utilisateur.
|
| Les noms correspondent aux contraintes définies dans le script SQL.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Contraintes Primary key
    |--------------------------------------------------------------------------
    */

    // la structure de projet ne permet pas de définir des noms de contrainte personnalisés puiqu'il n'y a pas de dossier etudiant
    // par contre puisque la seule table gérée est la table etudiant, on pourrait définir un nom de contrainte unique pour la clé primaire de cette table
    // mais ici cela ne sert à rien, la clé primaire étant de type compteur
    // 'primary' => "Un enregistrement avec cette clé primaire existe déjà.",


    /*
    |--------------------------------------------------------------------------
    | Contraintes UNIQUE
    |--------------------------------------------------------------------------
    */

    'uq_etudiant_nom_prenom' => "Un étudiant portant ce nom et ce prénom existe déjà.",

    /*
    |--------------------------------------------------------------------------
    | Contraintes CHECK
    |--------------------------------------------------------------------------
    */

    // ces deux contraintes sont déjà pris en charge par les objets ColumnText réprésentant ces champs avec la propriété required = true
    'ck_etudiant_nom' => "Le nom de l'étudiant est invalide.",
    'ck_etudiant_prenom' => "Le prénom de l'étudiant est invalide.",

    // cette contrainte est définie dans le script SQL de création de la table etudiant,
    // mais l'utilisation d'un objet ColumnList permet de vérifier préalablement la valeur
    'ck_etudiant_sexe' => "Le sexe de l'étudiant doit être M ou F.",


    /*
    |--------------------------------------------------------------------------
    | Contraintes FOREIGN KEY
    |--------------------------------------------------------------------------
    */

     // la contrainte fk_etudiant_option est définie dans le script SQL de création de la table etudiant,
    // mais la suppression d'une option ne peut pas être effectuée, donc elle n'est pas utilisée dans le code PHP
];
