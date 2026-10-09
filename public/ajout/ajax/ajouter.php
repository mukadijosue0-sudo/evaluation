<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
// Requete::exigerPost();

// récupération des données transmises
$columns = Requete::postArray('columns');

// création de l'objet métier
$etudiant = new Etudiant();

// ajout de la catégorie
$resultat = $etudiant->add($columns);

if ($resultat === true) {
    ReponseJson::envoyerMessage("Étudiant ajoutée");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($etudiant->getErrors());