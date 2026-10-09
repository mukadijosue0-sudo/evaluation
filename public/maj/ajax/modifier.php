<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
// Requete::exigerPost();

// récupération des données transmises
$id = Requete::postInt('primaryKey');
$columns = Requete::postArray('columns');

// ce contrôleur ne doit accepter que des modifications sur les colonnes suivantes
$colonnesAutorisees = ['nom', 'prenom', 'dateNaissance', 'sexe'];
$erreur = false;
foreach ($columns as $colonne => $valeur) {
    if (!in_array($colonne, $colonnesAutorisees)) {
        $lesErreurs[$colonne] = "Cette colonne n'est pas autorisée pour la modification";
        $erreur = true;
    }
}
if ($erreur) {
    ReponseJson::envoyerLesErreurs($lesErreurs);
}

// création de l'objet métier
$etudiant = new Etudiant();

// modification du Etudiant
$resultat = $etudiant->modify($id, $columns);

if ($resultat === true) {
    ReponseJson::envoyerMessage("Étudiant modifié");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($etudiant->getErrors());