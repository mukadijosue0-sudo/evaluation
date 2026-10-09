<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion d'un fichier téléversé.
 *
 * Vérifie un fichier reçu avant son traitement.
 *
 * @author Guy Verghote
 * @version 2026.3
 * @date    08/10/2026
 */
class InputFile extends Input
{
    // Fichier reçu.
    protected array $file;
    // Extensions autorisées.
    protected array $lesExtensions = [];
    // Types MIME autorisés.
    protected array $lesTypes = [];
    // Taille maximale.
    protected int $maxSize = 0;

    /**
     * Constructeur.
     */
    public function __construct(array $fichier, $lesParametres)
    {
        parent::__construct();

        if (!isset($fichier['name'], $fichier['tmp_name'], $fichier['error'], $fichier['size'])) {
            throw new UserException("Le fichier doit contenir les clés : name, tmp_name, error et size.");
        }

        $this->file = $fichier;
        $this->setValue((string)$fichier['name']);

        if (isset($lesParametres['maxSize'])) {
            $this->maxSize = max(0, ((int)$lesParametres['maxSize']));
        }


        if (isset($lesParametres['lesExtensions']) && is_array($lesParametres['lesExtensions'])) {
            $this->lesExtensions = array_map('strtolower', $lesParametres['lesExtensions']);
        }

        if (isset($lesParametres['lesTypes']) && is_array($lesParametres['lesTypes'])) {
            $this->lesTypes = array_map('strtolower', $lesParametres['lesTypes']);
        }

        return $this;
    }

    /**
     * Retourne le fichier reçu.
     */
    public function getFile(): array
    {
        return $this->file;
    }

    /**
     * Retourne le nom d'origine.
     */
    public function getName(): string
    {
        return (string)$this->file['name'];
    }

    /**
     * Retourne le fichier temporaire.
     */
    public function getTmpName(): string
    {
        return (string)$this->file['tmp_name'];
    }

    /**
     * Retourne la taille.
     */
    public function getSize(): int
    {
        return (int)$this->file['size'];
    }

    /**
     * Retourne le code erreur.
     */
    public function getError(): int
    {
        return (int)$this->file['error'];
    }

    /**
     * Retourne l'extension.
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
    }

    /**
     * Retourne le type MIME.
     */
    public function getMimeType(): string
    {
        if (!is_file($this->getTmpName())) {
            return '';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return '';
        }

        $mime = finfo_file($finfo, $this->getTmpName());
        finfo_close($finfo);

        return $mime === false ? '' : strtolower($mime);
    }

    /**
     * Vérifie le fichier.
     */
    public function checkValidity(): bool
    {

        if (!$this->verifierTeleversement()) {
            return false;
        }

        if (!$this->verifierTaille()) {
            return false;
        }

        if (!$this->verifierExtension()) {
            return false;
        }

        if (!$this->verifierTypeMime()) {
            return false;
        }

        if (!$this->validationSpecifique()) {
            return false;
        }

        if (!$this->preparerNomFichier()) {
            return false;
        }


        return true;
    }

    /**
     * Validation complémentaire.
     */
    protected function validationSpecifique(): bool
    {
        return true;
    }

    /**
     * Vérifie le téléversement.
     */
    protected function verifierTeleversement(): bool
    {
        if ($this->getError() === UPLOAD_ERR_NO_FILE) {
            if ($this->isRequired()) {
                $this->validationMessage = "Aucun fichier n'a été téléversé.";
                return false;
            }

            return true;
        }

        if ($this->getError() !== UPLOAD_ERR_OK) {
            $this->validationMessage = $this->getMessageErreurUpload();
            return false;
        }

        if (!is_file($this->getTmpName())) {
            $this->validationMessage = "Le fichier temporaire est introuvable.";
            return false;
        }

        // Sécurité : contrôle que le fichier vient bien d'un téléversement HTTP POST
        if (!is_uploaded_file($this->getTmpName())) {
            $this->validationMessage = "Le fichier n'est pas un fichier téléversé valide.";
            return false;
        }


        return true;
    }

    /**
     * Message erreur upload.
     */
    protected function getMessageErreurUpload(): string
    {
        return match ($this->getError()) {
            UPLOAD_ERR_INI_SIZE => "La taille du fichier dépasse la limite PHP.",
            UPLOAD_ERR_FORM_SIZE => "La taille du fichier dépasse la limite du formulaire.",
            UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement téléversé.",
            UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été téléversé.",
            UPLOAD_ERR_NO_TMP_DIR => "Le dossier temporaire PHP est absent.",
            UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire le fichier sur le disque.",
            UPLOAD_ERR_EXTENSION => "Une extension PHP a interrompu le téléversement.",
            default => "Une erreur inconnue est survenue lors du téléversement."
        };
    }

    /**
     * Vérifie la taille.
     */
    protected function verifierTaille(): bool
    {
        if ($this->maxSize === 0 || $this->getSize() <= $this->maxSize) {
            return true;
        }

        $this->validationMessage = "La taille du fichier (" . $this->getSize() . " octets) dépasse la taille autorisée (" . $this->maxSize . " octets).";

        return false;
    }

    /**
     * Vérifie l'extension.
     */
    protected function verifierExtension(): bool
    {
        $extension = $this->getExtension();

        if (in_array($extension, $this->lesExtensions, true)) {
            return true;
        }

        $this->validationMessage = "L'extension .$extension n'est pas autorisée.";

        return false;
    }

    /**
     * Vérifie le type MIME.
     */
    protected function verifierTypeMime(): bool
    {
        $mime = $this->getMimeType();

        if (in_array($mime, $this->lesTypes, true)) {
            return true;
        }

        $this->validationMessage = "Le type MIME '$mime' n'est pas autorisé.";

        return false;
    }

    /**
     * Prépare le nom du fichier.
     */
    protected function preparerNomFichier(): bool
    {
        $nom = $this->getName();
        $nom = $this->retirerAccent($nom);
        $nom = strtolower($nom);
        $nom = $this->nettoyerNom($nom);
        if ($nom === '') {
            $this->validationMessage = "Le nom du fichier est invalide.";
            return false;
        }
        $this->setValue($nom);
        return true;
    }


    /**
     * Supprime les accents.
     *
     * @param string $nom Le nom à traiter
     * @return string Le nom sans accents
     */
    protected function retirerAccent(string $nom): string
    {
        // Méthode principale : extension Intl.
        if (class_exists(\Transliterator::class)) {
            $resultat = transliterator_transliterate(
                'Any-Latin; Latin-ASCII',
                $nom
            );

            if ($resultat !== false) {
                return $resultat;
            }
        }

        // Méthode de secours : iconv.
        $resultat = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nom);

        if ($resultat !== false) {
            return $resultat;
        }

        // Dernière solution : remplacement manuel des caractères accentués.
        return strtr($nom, [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A',
            'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ø' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ý' => 'Y',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'Œ' => 'OE', 'œ' => 'oe',
            'Š' => 'S', 'š' => 's',
            'Ž' => 'Z', 'ž' => 'z',
        ]);
    }


    /**
     * Nettoie le nom : le nom de base ne conserve que [a-z0-9_-] (espaces remplacés par _),
     * l'extension est conservée après un unique point.
     */
    protected function nettoyerNom(string $nom): string
    {
        $base = pathinfo($nom, PATHINFO_FILENAME);
        $extension = pathinfo($nom, PATHINFO_EXTENSION);

        // Passage en minuscules pour uniformiser la casse
        $base = mb_strtolower($base, 'UTF-8');
        $extension = mb_strtolower($extension, 'UTF-8');

        // Remplacement des espaces ET des apostrophes par des underscores
        $base = preg_replace('/[\s\']+/', '_', trim($base)) ?? '';

        // Suppression des caractères non autorisés (conservation des tirets du bas, tirets et alphanumériques)
        $base = preg_replace('/[^a-z0-9_-]/', '', $base) ?? '';

        // Suppression des caractères non autorisés dans l'extension
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?? '';

        if ($base === '') {
            return '';
        }

        return $extension === '' ? $base : $base . '.' . $extension;
    }
}