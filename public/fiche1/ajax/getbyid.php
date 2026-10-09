<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// ce contrôleur ne répond qu'à une requête GET
Requete::exigerGet();

// récupération et contrôle de l'identifiant (entier, sinon UserException)
$id = Requete::getInt('id');

$etudiant = Etudiant::getById($id);

if ($etudiant === []) {
    ReponseJson::envoyerErreur("Étudiant introuvable.", 404);
}

ReponseJson::envoyerLesDonnees($etudiant);