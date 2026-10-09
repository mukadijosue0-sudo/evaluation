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
$id = Requete::postString('primaryKey');

// création de l'objet métier
$etudiant= new Etudiant();

// suppression de l'étudiant
$resultat = $etudiant->delete($id);

if ($resultat === true) {
    ReponseJson::envoyerMessage("Étudiant supprimé");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($etudiant->getErrors());