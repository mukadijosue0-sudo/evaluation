<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// toutes les données de tous les étudiants sont chargées une seule fois par le contrôleur
$page = new Page();
$page->setTitre("Fiche d'un étudiant ")
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->afficher();