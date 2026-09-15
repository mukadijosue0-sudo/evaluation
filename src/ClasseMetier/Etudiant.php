<?php

declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\ColumnDate;
use ClasseTechnique\ColumnList;
use ClasseTechnique\ColumnText;
use ClasseTechnique\ContrainteUnique;
use ClasseTechnique\Select;
use ClasseTechnique\Table;
use ClasseTechnique\TextCase;

/**
 * Gestion des étudiants.
 *
 * Cette classe représente la table etudiant.
 *
 * Responsabilités :
 *  - définition des colonnes et de leurs règles de validation ;
 *  - définition des contraintes métier ;
 *  - consultations spécifiques des étudiants.
 *
 * Les opérations CRUD génériques sont fournies par la classe Table.
 *
 * @author Guy Verghote
 * @version 2026.3
 */
class Etudiant
{
    /**
     * Répertoire où sont stockées les photos des étudiants.
     */
    public const string DOSSIER_PHOTO_ETUDIANT = DOSSIER_WWW . '/data/photo/';


    /**
     * Retourne l'ensemble des informations sur les étudiants.
     *
     * @return array
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
            select
                etudiant.id,
                nom,
                prenom,
                concat(nom, ' ', prenom) as nomPrenom,
                dateNaissance,
                date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr,
                sexe,
                libelleCourt,
                photo
            from etudiant
                join options on etudiant.idOption = options.id
            order by nom, prenom
        SQL;

        $select = new Select();

        $lignes = $select->getRows($sql);

        foreach ($lignes as &$ligne) {
            $ligne['present'] =
                isset($ligne['photo'])
                && is_file(
                    self::DOSSIER_PHOTO_ETUDIANT . $ligne['photo']
                );
        }

        return $lignes;
    }


    /**
     * Retourne la liste simplifiée des étudiants.
     *
     * Cette méthode est notamment destinée à alimenter une liste
     * de sélection dans l'interface.
     *
     * @return array
     */
    public static function getListe(): array
    {

    }


    /**
     * Retourne les informations d'un étudiant à partir de son identifiant.
     *
     * @param int $id Identifiant de l'étudiant.
     *
     * @return array
     */
    public static function getById(int $id): array
    {

    }


    /**
     * Retourne les étudiants correspondant à un nom ou prénom.
     *
     * La recherche est effectuée sur la concaténation du nom et du prénom.
     *
     * @param string $nomPrenom
     *
     * @return array
     */
    public static function getByNomPrenom(string $nomPrenom): array
    {

    }


}
