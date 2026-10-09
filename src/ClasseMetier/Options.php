<?php

declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\Database;
use ClasseTechnique\Select;
use PDO;

/**
 * Gestion des options.
 *
 * Cette classe représente la table options.
 *
 * @author Guy Verghote
 * @version 2026.3
 */
class Options
{

    /**
     * Retourne les identifiants des options disponibles.
     *
     * Cette méthode est utilisée pour construire la liste des valeurs
     * autorisées pour la colonne idOption.
     *
     * @return array
     */
    public static function getLesIds(): array
    {
        $sql = 'select id from options order by id';
        $db = Database::getInstance();
        $cmd = $db->query($sql);
        $lesIds = $cmd->fetchAll(PDO::FETCH_COLUMN);
        $cmd->closeCursor();
        return $lesIds;
    }

    /**
     * Retourne le nombre d'étudiants
     * @return int
     */
    public static function getLesOptions(): array
    {
        $sql = "SELECT id, libelleLong FROM options ORDER BY libelleLong;";
        $select = new Select();
        return $select->getRows($sql);
    }
}
