<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion d'un fichier image téléversé.
 *
 * Ajoute les contrôles spécifiques aux images.
 * @author Guy Verghote
 * @version 2026.2
 * @date    05/10/2026
 */
class InputFileImg extends InputFile
{

    // Largeur maximale autorisée.
    protected int $maxWidth = 0;

    // Hauteur maximale autorisée.
    protected int $maxHeight = 0;

    // L'image doit-elle être redimensionnée.
    protected bool $resized = false;

    /**
     * Constructeur.
     * @param array $fichier Données du fichier téléversé.
     * @param array $lesParametres Paramètres de configuration.
     */
    public function __construct(array $fichier, $lesParametres)
    {
        parent::__construct($fichier, $lesParametres);

        if (isset($lesParametres['maxWidth'])) {
            $this->maxWidth = (int)$lesParametres['maxWidth'];
        }

        if (isset($lesParametres['maxHeight'])) {
            $this->maxHeight = (int)$lesParametres['maxHeight'];
        }

        if (isset($lesParametres['resized'])) {
            $this->resized = (bool)$lesParametres['resized'];
        }
        return $this;
    }

    /**
     * Retourne la largeur maximale autorisée.
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * Retourne la hauteur maximale autorisée.
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * Retourne si l'image doit être redimensionnée.
     * @return bool
     */
    public function getResized(): bool
    {
        return $this->resized;
    }


    /**
     * Validation spécifique aux images.
     *
     * @return bool
     * @throws UserException
     * @see InputFile::checkValidity()
     * Appelée automatiquement par InputFile::checkValidity()
     */
    protected function validationSpecifique(): bool
    {
        // Si l'image doit être redimensionnée, on ne fait pas de vérification de taille.
        if ($this->resized) {
            return true;
        }

        // Récupération des dimensions de l'image.
        $infos = getimagesize($this->getTmpName());

        // Vérification que le fichier est bien une image.
        if ($infos === false) {
            $this->validationMessage = "Le fichier n'est pas une image valide.";
            return false;
        }

        // Vérification des dimensions de l'image.
        $largeur = $infos[0];
        $hauteur = $infos[1];

        // Vérification de la largeur maximale.
        if ($this->maxWidth > 0 && $largeur > $this->maxWidth) {
            $this->validationMessage = "La largeur maximale de l'image est de {$this->maxWidth}px.";
            return false;
        }

        // Vérification de la hauteur maximale.
        if ($this->maxHeight > 0 && $hauteur > $this->maxHeight) {
            $this->validationMessage = "La hauteur maximale de l'image est de {$this->maxHeight}px.";
            return false;
        }

        return true;
    }
}