<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Config;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// paramètres appliqués aux photos (config/etudiant.php)
$parametres = Config::chargerPhp('etudiant');

// le navigateur a besoin de l'URL publique du dossier, pas de son chemin sur le disque
$parametres['repertoire'] = '/data/photo';

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Gestion des photos des étudiants")
    ->setDonnee('lesEtudiants', Etudiant::getAll())
    ->setDonnee('parametres', $parametres)
    ->afficher();