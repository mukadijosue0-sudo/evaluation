<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestionnaire de stockage de fichiers sur disque.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 07/10/2026
 */
class FileManager
{
    protected string $repertoire;
    // Si le fichier existe déjà, doit-il être renommé ?
    protected bool $renommerSiExiste = false;
    // Extensions autorisées
    protected array $lesExtensions = [];

    /**
     * @param string $repertoire Répertoire de stockage.
     * @param array $parametres Clés optionnelles : 'renommerSiExiste', 'lesExtensions'.
     */
    public function __construct(string $repertoire, array $parametres = [])
    {
        $this->repertoire = rtrim($repertoire, '/\\');
        $this->renommerSiExiste = (bool)($parametres['renommerSiExiste'] ?? false);
        $this->lesExtensions = $parametres['lesExtensions'] ?? [];
    }

    /**
     * Retourne le chemin du répertoire géré.
     */
    public function getRepertoire(): string
    {
        return $this->repertoire;
    }

    /**
     * Retourne la liste des fichiers présents dans le répertoire.
     */
    public function getLesFichiers(): array
    {
        $lesFichiers = [];

        if (is_dir($this->repertoire)) {
            $contenu = scandir($this->repertoire);
            if ($contenu !== false) {
                foreach ($contenu as $element) {
                    if ($element !== '.' && $element !== '..' && is_file($this->repertoire . DIRECTORY_SEPARATOR . $element)) {

                        // Si un filtre d'extensions est configuré, on vérifie l'extension du fichier
                        if (!empty($this->lesExtensions)) {
                            $extension = pathinfo($element, PATHINFO_EXTENSION);
                            if (!in_array($extension, $this->lesExtensions, true)) {
                                continue;
                            }
                        }

                        $lesFichiers[] = $element;
                    }
                }
            }
        }
        return $lesFichiers;
    }

    /**
     * Vérifie si un fichier existe physiquement sur le disque.
     */
    public function existe(string $nom): bool
    {
        return $this->nomSecurise($nom) && is_file($this->repertoire . DIRECTORY_SEPARATOR . $nom);
    }

    /**
     * Génère un nom de fichier unique en ajoutant un suffixe numérique si nécessaire.
     * @param string $nom Le nom du fichier à rendre unique.
     * @return string Le nom de fichier unique.
     */
    public function genererNomUnique(string $nom): string
    {
        $baseName = pathinfo($nom, PATHINFO_FILENAME);
        $extension = pathinfo($nom, PATHINFO_EXTENSION);
        $nb = 1;
        $nouveauNom = $nom;

        while ($this->existe($nouveauNom)) {
            $nouveauNom = $baseName . '(' . $nb . ')' . ($extension ? '.' . $extension : '');
            $nb++;
        }

        return $nouveauNom;
    }

    /**
     * Ajoute un fichier dans le répertoire du stockage.
     * Si le fichier existe déjà, il est renommé (paramètre 'renommerSiExiste') ou une exception est levée.
     * @param string $source Chemin du fichier source à ajouter.
     * @param string $nom Nom souhaité du fichier.
     * @return string Le nom réellement utilisé pour le stockage.
     * @throws UserException Si le fichier existe déjà et ne doit pas être renommé, ou si la copie échoue.
     */
    public function copier(string $source, string $nom): string
    {
        if (!$this->nomSecurise($nom)) {
            Journal::enregistrer("Tentative de copie avec un nom de fichier invalide : " . addcslashes($source, "\0..\37") . " -> " . addcslashes($nom, "\0..\37"));
            throw new UserException("Nom de fichier invalide.");
        }
        $nom = $this->preparerNom($nom);
        $destination = $this->repertoire . DIRECTORY_SEPARATOR . $nom;
        if (!copy($source, $destination)) {
            throw new UserException("La copie du fichier $source vers $destination a échoué.");
        }

        @unlink($source);
        return $nom;
    }

    /**
     * Retourne le nom à utiliser : unique si le renommage est activé, sinon le nom demandé.
     * @throws UserException Si le fichier existe déjà et ne doit pas être renommé.
     */
    protected function preparerNom(string $nom): string
    {
        if ($this->renommerSiExiste) {
            return $this->genererNomUnique($nom);
        }
        if ($this->existe($nom)) {
            throw new UserException("Le fichier $nom existe déjà.");
        }
        return $nom;
    }

    /**
     * Supprime un fichier du disque s'il existe.
     */
    public function supprimer(string $nom): bool
    {
        if (!$this->nomSecurise($nom)) {
            Journal::enregistrer("Tentative de suppression avec un nom de fichier invalide : " . addcslashes($nom, "\0..\37"));
            throw new UserException("Nom de fichier invalide.");
        }

        if (!$this->existe($nom)) {
            Journal::enregistrer("Le fichier $nom n'existe pas, suppression ignorée.");
            return true; // Déjà absent, opération considérée comme réussie
        }

        if (!is_writable($this->repertoire . DIRECTORY_SEPARATOR . $nom)) {
            Journal::enregistrer("Le fichier $nom n'est pas supprimable.");
            return false;
        }

        if (!@unlink($this->repertoire . DIRECTORY_SEPARATOR . $nom)) {
            Journal::enregistrer("La suppression du fichier $nom a échoué.");
            return false;
        }
        return true;
    }

    /**
     * Remplace un fichier existant sur le disque.
     *
     * @param string $source Chemin du fichier temporaire/source à copier
     * @param string $nom Nom du fichier cible dans le répertoire
     *
     * @throws UserException Si la copie ou le remplacement échoue
     */
    public function remplacer(string $source, string $nom): void
    {
        if (!$this->nomSecurise($nom)) {
            Journal::enregistrer("Tentative de remplacement vers un fichier invalide : " . addcslashes($source, "\0..\37") . " -> " . addcslashes($nom, "\0..\37"));
            throw new UserException("Nom de fichier à remplacer invalide.");
        }
        $destination = $this->repertoire . DIRECTORY_SEPARATOR . $nom;
        if (!$this->existe($nom)) {
            throw new UserException("Le fichier $nom n'existe pas.");
        }
        // Tente la copie (écrase automatiquement la destination si le fichier existe déjà)
        if (!copy($source, $destination)) {
            throw new UserException("Le remplacement du fichier $nom a échoué.");
        }

        // Nettoyage du fichier temporaire
        @unlink($source);
    }

    /**
     * Vérifie qu'un nom ne peut pas sortir du répertoire (traversée de répertoire).
     * Refuse séparateurs de chemin, ':', caractères de contrôle, '.' et '..'.
     */
    protected function nomSecurise(string $nom): bool
    {
        return $nom !== ''
            && $nom !== '.'
            && $nom !== '..'
            && !preg_match('/[\/\\\\:\x00-\x1F]/', $nom)
            && basename($nom) === $nom;
    }


    /**
     * Valide la syntaxe d'un nom de fichier.
     */
    protected function verifierNomFichier(string $nom): bool
    {
        return $this->nomSecurise($nom) && (bool)preg_match('/^[a-zA-Z0-9_\-\.]+$/', $nom);
    }
}