<?php
declare(strict_types=1);

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;
use ClasseMetier\Etudiant;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
// Requete::exigerPost();

// récupération du paramètre transmis
$id = Requete::postInt('id');

$etudiant = Etudiant::getById($id);

if (!$etudiant) {
    ReponseJson::envoyerLesErreurs(['id' => 'ID inexistant.']);
}

// envoi de la réponse
ReponseJson::envoyerLesDonnees($etudiant);
