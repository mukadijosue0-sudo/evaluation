<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe abstraite représentant une colonne métier.
 * Elle encapsule la valeur, les règles de validation et les contraintes nécessaires aux opérations CRUD.
 *
 * @Author : Guy Verghote
 * @Version 2026.3
 * @Date : 12/08/2026
 */
abstract class Column
{
    // La valeur de l'objet
    public mixed $Value = null;

    // La valeur est-elle obligatoire ?
    public readonly bool $Required;

    // La colonne est-elle utilisable lors d'une opération d'ajout ?
    public readonly bool $Insertable;

    // La colonne est-elle utilisable lors d'une opération de modification ?
    public readonly bool $Updatable;

    // message d'erreur associé à la colonne
    protected string $validationMessage;

    /**
     * Constructeur.
     */
    public function __construct(bool $required = true, bool $insertable = true, bool $updatable = true)
    {
        $this->Required = $required;
        $this->Insertable = $insertable;
        $this->Updatable = $updatable;
        $this->Value = null;
        $this->validationMessage = '';
    }

    /**
     * Accesseur sur le message d'erreur.
     */
    public function getValidationMessage(): string
    {
        return $this->validationMessage;
    }

    /**
     * Vérifie que la valeur est renseignée quand la propriété Require est vraie.
     */
    public function checkValidity(): bool
    {
        if ($this->Required && ($this->Value === null || strlen(trim((string)$this->Value)) === 0)) {
            $this->validationMessage = "Veuillez renseigner ce champ.";
            return false;
        }

        return true;
    }
}
