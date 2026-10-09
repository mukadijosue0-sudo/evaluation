<?php
declare(strict_types=1);

use ClasseTechnique\Config;
use ClasseTechnique\Erreur;

// ==========================================================
// Initialisation générale
// ==========================================================

date_default_timezone_set('Europe/Paris');

// ==========================================================
// Gestion de session
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================================
// Définition des chemins du projet
// ==========================================================

// Répertoire public accessible par le navigateur
define('DOSSIER_WWW', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public');

// Racine complète du projet
define('DOSSIER_RACINE', dirname(DOSSIER_WWW));

// Répertoire contenant les fichiers de configuration
define('DOSSIER_CONFIG', DOSSIER_RACINE . DIRECTORY_SEPARATOR . 'config');


// ==========================================================
// Chargement automatique des classes
// ==========================================================

require DOSSIER_RACINE . '/vendor/autoload.php';


// ==========================================================
// Gestion globale des erreurs
// ==========================================================

Erreur::installerGestionnaire();


