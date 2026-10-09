<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Config;
use ClasseTechnique\FileManager;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
// Requete::exigerPost();

// récupération des données transmises
$id = Requete::postString('primaryKey');

// récupération du nom de la photo AVANT la suppression de l'étudiant
$photo = Etudiant::getById((int)$id)['photo'] ?? null;

// création de l'objet métier
$etudiant = new Etudiant();

// suppression de l'étudiant
$resultat = $etudiant->delete($id);

if ($resultat === true) {

    // suppression de la photo associée sur le disque, s'il y en a une
    if ($photo !== null) {
        $config = Config::chargerPhp('etudiant');
        $fileManager = new FileManager($config['repertoire']);
        $fileManager->supprimer($photo);
    }

    ReponseJson::envoyerMessage("Étudiant supprimé");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($etudiant->getErrors());