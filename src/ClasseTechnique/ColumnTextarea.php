<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnTextarea : contrôle une chaîne de caractères multiligne.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 12/08/2026
 */
class ColumnTextarea extends Column
{
    // Encode le contenu HTML.
    public readonly bool $EncoderHtml;

    // Autorise le contenu HTML.
    public readonly bool $AcceptHtml;

    // Balises HTML autorisées.
    public readonly array $BalisesAutorisees;

    /**
     * Constructeur.
     */
    public function __construct(
        bool $required = true,
        bool $insertable = true,
        bool $updatable = true,
        bool $encoderHtml = false,
        bool $acceptHtml = true,
        array $balisesAutorisees = [
            '<br>',
            '<span>',
            '<b>',
            '<i>',
            '<strong>',
            '<ul>',
            '<li>',
            '<img>',
            '<a>',
            '<div>'
        ]
    ) {
        parent::__construct(
            required: $required,
            insertable: $insertable,
            updatable: $updatable
        );

        $this->EncoderHtml = $encoderHtml;
        $this->AcceptHtml = $acceptHtml;
        $this->BalisesAutorisees = $balisesAutorisees;
    }

    /**
     * Vérifie la validité du contenu multiligne.
     */
    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        // Une valeur facultative peut rester vide.
        if ($this->Value === null || $this->Value === '') {
            return true;
        }

        $valeur = (string)$this->Value;

        // Encodage complet du contenu HTML.
        if ($this->EncoderHtml) {
            $this->Value = htmlspecialchars(
                $valeur,
                ENT_QUOTES,
                'UTF-8'
            );

            return true;
        }

        // Suppression des balises HTML non autorisées.
        if (!$this->AcceptHtml) {
            $this->Value = strip_tags(
                $valeur,
                $this->BalisesAutorisees
            );

            return true;
        }

        // Le contenu HTML est conservé tel quel.
        $this->Value = $valeur;

        return true;
    }
}