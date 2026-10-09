<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Config;
use ClasseTechnique\ImageManager;
use ClasseTechnique\InputFileImg;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé


// Récupération des données de la requête


// Vérification de la présence du fichier



// Recherche de l'étudiant pour vérifier son existence et récupérer le photo actuelle




// Instanciation d'un nouvel objet InputFileImg



// Vérification de la validité du fichier image



// Instanciation d'un objet FileManager


// Sauvegarde physique de la nouvelle photo (nom d'origine, renommé si nécessaire)


// Instanciation de l'objet métier


// Mise à jour de la base de données avec le nom de la nouvelle photo
// Si la mise à jour de la BDD échoue, suppression de la photo créée sur le disque







// Si la BDD est mise à jour avec succès et qu'il existe une ancienne photo, suppression de l'ancienne photo du disque


// Envoi de la réponse JSON : envoi du nom de la nouvelle photo pour mise à jour côté client



