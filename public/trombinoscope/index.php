<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Trombinoscope des étudiants")
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->addScript("/composant/html2pdf/html2pdf.bundle.min.js")
    ->afficher();


