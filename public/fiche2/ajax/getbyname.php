<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";


// Vérification d'un appel Ajax en GET
Requete::exigerGet();

$search = Requete::getString('search');

if (trim($search) === '') {
    ReponseJson::envoyerLesDonnees([]);
}

if (!preg_match("/^[\p{L} '-]+$/u", $search)) {
    ReponseJson::envoyerLesDonnees([]);
}

ReponseJson::envoyerLesDonnees(Etudiant::getByNomPrenom($search));
