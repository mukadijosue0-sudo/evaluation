<?php
declare(strict_types=1);

namespace ClasseTechnique;

use PDOException;
use Throwable;
use JetBrains\PhpStorm\NoReturn;

/**
 * Classe Erreur : gestion centralisée des erreurs applicatives et SQL.
 *
 * STRATÉGIE :
 * _ Toute exception est journalisée avec ses détails techniques complets
 * - Réponse utilisateur en deux cas seulement :
 *   • UserException → le message réel
 *   • Tout autre Throwable → message générique
 *
 * @author Guy Verghote
 * @Version 2026.4
 * @Date : 28/08/2026
 */
enum TypeReponse: string
{
    // Réponse HTML classique (redirection vers page d'erreur)
    case HTML = 'html';
    // Réponse JSON (API clients)
    case JSON = 'json';
}

class Erreur
{
    // Message générique affiché pour toute erreur non-applicative
    private const string MSG_SYSTEME = "Une erreur technique est survenue, veuillez réessayer ultérieurement.";

    // Mapping des codes d'erreur MySQL/MariaDB connus vers un message lisible
    // Ces messages sont présentés comme UserException après journalisation
    private static array $lesCodesSql = [
        1062 => "Enregistrement déjà existant.",
        1048 => "Une information obligatoire est manquante.",
        1406 => "Une information est trop longue.",
        1366 => "Format de donnée invalide.",
        1452 => "Donnée invalide (référence inexistante).",
        1451 => "Suppression impossible : donnée utilisée.",
        3819 => "Une valeur saisie ne respecte pas une règle de validation.",
        4025 => "Une valeur saisie ne respecte pas une règle de validation.",
    ];

    // Tableau qui contiendra après chargement du fichier de configuration config/contrainte.php lié à l'application
    private static array $lesContraintes = [];

    /**
     * Définit les contraintes spécifiques à l'application.
     *
     * Le tableau attendu doit être associatif : ['nom_contrainte' => 'message utilisateur'].
     *
     * En cas de configuration incorrecte, l'erreur est journalisée
     * et aucune contrainte spécifique n'est chargée.
     *
     * @param array $lesContraintes
     */
    public static function definirLesContraintes(array $lesContraintes): void
    {
        // Vérifie que le tableau possède bien des clés associatives
        if (array_is_list($lesContraintes)) {
            Journal::enregistrer("Le fichier de configuration des contraintes doit contenir un tableau associatif.", 'erreur');
            self::$lesContraintes = [];
            return;
        }

        // Vérifie le contenu de chaque contrainte
        foreach ($lesContraintes as $nom => $message) {
            if ($nom === '' || !is_string($nom)) {
                Journal::enregistrer("Nom de contrainte invalide dans la configuration.", 'erreur');
                self::$lesContraintes = [];
                return;
            }

            if (!is_string($message) || $message === '') {
                Journal::enregistrer("Message invalide pour la contrainte '$nom'.", 'erreur');

                self::$lesContraintes = [];
                return;
            }
        }

        self::$lesContraintes = $lesContraintes;
    }

    /**
     * Installe le handler global ; c'est le seul point d'entrée public.
     */
    public static function installerGestionnaire(): void
    {
        set_exception_handler(static function (Throwable $e): void {
            self::traiterReponse($e);
        });
    }

    /**
     * Traitement interne de toutes les exceptions.
     *
     * 1. Journalisation systématique des détails techniques
     * 2. Construction de la réponse utilisateur :
     *    UserException : message réel
     *    PDOException : message lisible si code connu, sinon générique
     *    Throwable: message générique
     */
    private static function traiterReponse(Throwable $e): void
    {
        if ($e instanceof UserException) {
            /*
             * Une erreur métier est volontairement destinée
             * à l'utilisateur.
             *
             * Elle n'est pas journalisée par défaut.
             */
            $message = $e->getMessage();
            $codeHttp = $e->getCodeHttp();
        } elseif ($e instanceof PDOException) {
            /*
             * Une erreur SQL est technique :
             * elle doit être journalisée.
             */
            self::journaliser($e);

            $message = self::resoudreMessageSQL($e);
            $codeHttp = 500;
        } else {
            /*
             * Toute autre exception est considérée
             * comme une erreur technique imprévue.
             */
            self::journaliser($e);

            $message = self::MSG_SYSTEME;
            $codeHttp = 500;
        }

        self::rendreReponse($message, $codeHttp);
    }

    /**
     * Journalisation systématique avec tous les détails techniques de l'exception.
     */
    private static function journaliser(Throwable $e): void
    {
        $detail = sprintf('[%s] %s | code : %s | fichier : %s | ligne : %d', get_class($e), $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine());
        Journal::enregistrer($detail, 'erreur');
    }

    /**
     * Résout un message lisible à partir d'une PDOException.
     *
     * PDO fournit errorInfo : [SQLSTATE, driverCode, driverMessage]
     *
     * Ordre de résolution :
     *  SQLSTATE '45000' => message fourni par un SIGNAL SQL
     *  Contrainte applicative définie dans config/contraintes.php
     *  Contrainte CHECK générique
     *  Code SQL connu dans $lesCodesSql
     *  Message système générique
     */
    private static function resoudreMessageSQL(PDOException $e): string
    {
        $errorInfo = $e->errorInfo ?? [];
        [$sqlState, $codeErreur, $message] = array_pad($errorInfo, 3, null);

        // Les triggers SQL peuvent renvoyer un message métier explicite
        if ($sqlState === '45000') {
            return (string)$message;
        }

        // Recherche d'une contrainte applicative configurée
        if (is_string($message)) {
            foreach (self::$lesContraintes as $nom => $libelle) {
                if (str_contains($message, $nom)) {
                    return $libelle;
                }
            }
        }

        // Gestion des CHECK
        if (self::estErreurCheckConstraint($codeErreur, $message)) {
            return "Une valeur saisie ne respecte pas une règle de validation.";
        }

        // Gestion des erreurs SQL connues
        if (isset(self::$lesCodesSql[(int)$codeErreur])) {
            return self::$lesCodesSql[(int)$codeErreur];
        }

        return self::MSG_SYSTEME;
    }

    /**
     * Détecte les violations de contraintes CHECK (MySQL code 3819, MariaDB code 4025).
     * Fallback sur le message texte si le code n'est pas exploitable.
     */
    private static function estErreurCheckConstraint(mixed $codeErreur, mixed $message): bool
    {
        if (in_array((int)$codeErreur, [3819, 4025], true)) {
            return true;
        }

        if (!is_string($message) || $message === '') {
            return false;
        }

        return (bool)preg_match('/check constraint|constraint .* is violated|violated/i', $message);
    }

    /**
     * Envoie la réponse dans le format attendu (JSON ou redirection HTML).
     */
    private static function rendreReponse(string $message, int $codeHttp): void
    {
        // Si une partie de la page a déjà été générée, elle est encore dans le buffer grâce à ob_start().
        //  On la supprime afin que la réponse d'erreur soit propre.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (self::getTypeReponse() === TypeReponse::JSON) {
            ReponseJson::envoyerErreur($message, $codeHttp);
        } else {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['erreur'] = $message;
            header('Location: /erreur');
            exit;
        }
    }

    /**
     * Détermine le type de réponse attendu : JSON si requête AJAX ou Accept contient application/json.
     */
    private static function getTypeReponse(): TypeReponse
    {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return TypeReponse::JSON;
        }

        if (
            isset($_SERVER['HTTP_ACCEPT']) &&
            str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
        ) {
            return TypeReponse::JSON;
        }

        return TypeReponse::HTML;
    }


    /**
     * Retourne un libellé lisible pour un code HTTP donné.
     */
    public static function getErreurHttp(int|string|null $codeHttp): string
    {
        if ($codeHttp === null) {
            return "Erreur HTTP : code inconnu";
        }

        $libelles = [
            400 => "Requête incorrecte",
            401 => "Erreur d'authentification",
            403 => "Demande interdite",
            404 => "Page non trouvée",
            405 => "Méthode non autorisée",
            408 => "Temps d'attente d'une requête dépassé",
            500 => "Erreur interne du serveur",
            502 => "Mauvaise passerelle",
            503 => "Service indisponible",
            504 => "Temps d'attente de la passerelle dépassé"
        ];

        return $libelles[$codeHttp] ?? "Erreur HTTP : " . $codeHttp;
    }
}

