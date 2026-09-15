<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ContrainteUnique
 *
 * Représente une règle d'unicité appliquée à une ou plusieurs colonnes
 * d'une table.
 *
 * Exemples :
 *
 *  - Une licence doit être unique :
 *      columns = ['licence']
 *
 *  - Un ensemble nom + prénom + date de naissance
 *    doit être unique :
 *      columns = ['nom', 'prenom', 'dateNaissance']
 *
 * Cette classe ne vérifie pas elle-même l'existence en base.
 * Elle contient uniquement la définition de la règle.
 *
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 12/08/2026
 */
readonly class ContrainteUnique
{
    /**
     * Liste des colonnes concernées par la contrainte
     *
     * Exemple :
     * ['nom', 'prenom', 'dateNaissance']
     */
    public array $columns;

    /**
     * Message retourné en cas de violation de la contrainte
     */
    public string $message;


    /**
     * Constructeur
     *
     * Les propriétés sont readonly car une contrainte unique
     * ne doit pas être modifiée après sa déclaration.
     *
     * @param array<string> $columns Colonnes participant à l'unicité
     * @param string $message Message d'erreur
     */
    public function __construct(array $columns, string $message)
    {
        $this->columns = $columns;
        $this->message = $message;
    }
}

