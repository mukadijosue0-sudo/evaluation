<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseMetier\Options;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
// chargement des catégories et du composant assurant la génération d'un document PDF à partir de la page HTML
$page = new Page();
$page->setTitre("Modification des  informations signalétiques d'un étudiant")
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->setDonnee("lesOptions", Options::getLesOptions())
    ->avecJeton()
    ->afficher();
