<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";


// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Fiche étudiant")
    ->avecJeton()
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->afficher();

