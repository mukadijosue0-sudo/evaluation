<?php

declare(strict_types=1);

namespace ClasseTechnique;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

/**
 * Classe ImageManager
 *
 * Gestionnaire spécialisé du stockage des fichiers images avec redimensionnement facultatif.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 02/10/2026
 */
class ImageManager extends FileManager
{
    protected int $maxWidth = 0;
    protected int $maxHeight = 0;
    protected bool $resized = false;

    /**
     * Constructeur.
     *
     * @param string $repertoire Répertoire de stockage des images.
     * @param array $parametres Tableau contenant les clés optionnelles : 'maxWidth', 'maxHeight', 'resized'.
     */
    public function __construct(string $repertoire, array $parametres = [])
    {
        parent::__construct($repertoire, $parametres);
        $this->maxWidth = $parametres['maxWidth'] ?? 0;
        $this->maxHeight = $parametres['maxHeight'] ?? 0;
        $this->resized = $parametres['resized'] ?? false;
    }

    /**
     * Redimensionne et enregistre l'image à sa destination.
     *
     * @return string Le nom réellement utilisé pour le stockage.
     * @throws UserException En cas d'échec du traitement ou de la sauvegarde.
     */
    public function copierImage(InputFileImg $file, string $nouveauNom): string
    {
        // Si le redimensionnement n'est pas activé ou qu'aucune dimension maximale n'est fixée,
        // on délègue la copie simple à FileManager (qui gère l'exception et le unlink).
        if (!$this->resized || ($this->maxWidth === 0 && $this->maxHeight === 0)) {
            return $this->copier($file->getTmpName(), $nouveauNom);
        }

        $nouveauNom = $this->preparerNom($nouveauNom);
        $destination = $this->repertoire . DIRECTORY_SEPARATOR . $nouveauNom;

        try {
            $image = new ImageResize($file->getTmpName());

            if ($this->maxWidth > 0 && $this->maxHeight === 0) {
                if ($image->getSourceWidth() > $this->maxWidth) {
                    $image->resizeToWidth($this->maxWidth);
                }
            } elseif ($this->maxHeight > 0 && $this->maxWidth === 0) {
                if ($image->getSourceHeight() > $this->maxHeight) {
                    $image->resizeToHeight($this->maxHeight);
                }
            } else {
                $image->resizeToBestFit($this->maxWidth, $this->maxHeight);
            }

            $image->save($destination);
            @unlink($file->getTmpName());
            return $nouveauNom;
        } catch (ImageResizeException $e) {
            throw new UserException("Erreur lors du redimensionnement de l'image : " . $e->getMessage());
        } catch (\Throwable $e) {
            throw new UserException("Impossible de sauvegarder l'image sur le disque.");
        }
    }
}