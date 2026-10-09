<?php

declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\ColumnDate;
use ClasseTechnique\ColumnList;
use ClasseTechnique\ColumnText;
use ClasseTechnique\Database;
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
class Etudiant extends Table
{
    /**
     * Répertoire où sont stockées les photos des étudiants.
     */
    public const string DOSSIER_PHOTO_ETUDIANT = DOSSIER_WWW . '/data/photo/';


    /**
     * Configuration de la table.
     */
    protected function configure(): void
    {
        $this->table = 'etudiant';
        $this->primaryKey = 'id';

        // -----------------------------------------------------------------
        // Nom
        // -----------------------------------------------------------------

        $this->addColumn('nom', new ColumnText(
            required: true,
            pattern: "^[a-zA-Z]+([' \\-]?[a-zA-Z]+)*$",
            maxLength: 20,
            casse: TextCase::Upper,
            supprimerAccent: true
        ));


        // -----------------------------------------------------------------
        // Prénom
        // -----------------------------------------------------------------

        $this->addColumn('prenom', new ColumnText(
            required: true,
            pattern: "^[a-zA-ZÀ-ÿÂ-üçÇ]+([ '\\-][a-zA-ZÀ-ÿÂ-üçÇ]+)*$",
            maxLength: 20,
            supprimerAccent: false
        ));

        // -----------------------------------------------------------------
        // Sexe
        // -----------------------------------------------------------------

        $this->addColumn('sexe', new ColumnList(
            required: true,
            values: ['M', 'F']
        ));

        // -----------------------------------------------------------------
        // Date de naissance
        // -----------------------------------------------------------------

        $this->addColumn('dateNaissance', new ColumnDate(
            required: true,
            min: date('Y-m-d', strtotime('-25 year')),
            max: date('Y-m-d', strtotime('-17 year'))
        ));

        // -----------------------------------------------------------------
        // Option
        // -----------------------------------------------------------------

        $this->addColumn('idOption', new ColumnList(
            required: true,
            values: Options::getLesIds()
        ));

    }

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
            $ligne['present'] = isset($ligne['photo'])  && is_file(self::DOSSIER_PHOTO_ETUDIANT . $ligne['photo']);
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
        $sql = <<<SQL
            select
                id,
                concat(nom, ' ', prenom) as nomPrenom,
                idOption
            from etudiant
            order by nom, prenom
        SQL;

        $select = new Select();

        return $select->getRows($sql);
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
            where etudiant.id = :id
        SQL;

        $select = new Select();

        $lignes = $select->getRows(
            $sql,
            ['id' => $id]
        );

        if ($lignes === []) {
            return [];
        }

        $etudiant = $lignes[0];

        $etudiant['present'] =
            isset($etudiant['photo'])
            && is_file(
                self::DOSSIER_PHOTO_ETUDIANT . $etudiant['photo']
            );

        return $etudiant;
    }

    /**
     * Met à jour le nom du fichier photo d'un étudiant.
     */
    public static function enregistrerNomPhoto(int $id, ?string $photo): bool
    {
        $commande = Database::getInstance()->prepare(
            'update etudiant set photo = :photo where id = :id'
        );

        return $commande->execute([
            'photo' => $photo,
            'id' => $id
        ]);
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
            where concat(nom, ' ', prenom) like :terme
            order by etudiant.nom, etudiant.prenom
            limit 10
        SQL;

        $select = new Select();

        $lignes = $select->getRows(
            $sql,
            ['terme' => "%$nomPrenom%"]
        );

        foreach ($lignes as &$ligne) {
            $ligne['present'] =
                isset($ligne['photo'])
                && is_file(
                    self::DOSSIER_PHOTO_ETUDIANT . $ligne['photo']
                );
        }

        return $lignes;
    }
}
