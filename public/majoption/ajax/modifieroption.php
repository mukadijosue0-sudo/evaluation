<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
// Requete::exigerPost();

// récupération controlée des paramètres transmis
$id = Requete::postInt('primarykey');
$columns = Requete::postArray('columns');


// création de l'objet métier
$etudiant = new Etudiant();

// modification du Etudiant
$resultat = $etudiant->modify($id, $columns);


if ($resultat === true) {
    ReponseJson::envoyerMessage("L'option a été modifié");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($etudiant->getErrors());




