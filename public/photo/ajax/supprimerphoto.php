<?php
declare(strict_types=1);

use ClasseMetier\Etudiant;
use ClasseTechnique\Config;
use ClasseTechnique\FileManager;
use ClasseTechnique\ImageManager;
use ClasseTechnique\InputFileImg;
use ClasseTechnique\Journal;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé


// Récupération des données de la requête


// Recherche de l'étudiant pour vérifier son existence et récupérer la photo actuelle




// si le photo est null, on ne fait rien




// Création de l'objet métier


// Mise à jour de la colonne photo qui prend la valeur null




// Instanciation du FileManager pour supprimer le fichier photo de l'étudiant



// Suppression du fichier photo de l'étudiant du disque s'il existe
// Personnalisation du message en fonction des cas :
// - Si le fichier existe et est supprimé
// - Si le fichier existe mais n'est pas supprimé
// - Si le fichier n'existe pas

