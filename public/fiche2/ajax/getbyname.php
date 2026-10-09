<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// ce contrôleur ne répond qu'à une requête GET
Requete::exigerGet();

// récupération et contrôle du texte saisi (chaîne non vide, sinon UserException)
$search = Requete::getString('search');

// envoi de la liste des étudiants correspondants au format JSON
ReponseJson::envoyerLesDonnees(Etudiant::getByNomPrenom($search));