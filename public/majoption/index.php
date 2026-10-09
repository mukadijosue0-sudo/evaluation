<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseMetier\Options;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

$page = new Page();
$page->setTitre("Définir l'option")
    ->setDonnee("lesEtudiants", Etudiant::getListe())
    ->setDonnee("lesOptions", Options::getLesOptions())
    ->avecJeton()
    ->afficher();

