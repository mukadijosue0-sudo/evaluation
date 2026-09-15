<?php
declare(strict_types=1);

use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// aucune donnée à transmettre : la source de l'autocomplétion est alimentée par AJAX
$page = new Page();
$page->setTitre("Fiche d'un étudiant ")
    ->afficher();