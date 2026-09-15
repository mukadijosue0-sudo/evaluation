<?php

declare(strict_types=1);

namespace ClasseTechnique;

class Menu
{
    private array $items = [];
    private array $brand = [];

    private string $currentPath;

    /**
     * Constructeur
     *
     * @param string $jsonFile Chemin vers le fichier JSON du menu
     */
    public function __construct(string $jsonFile)
    {
        $this->currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->charger($jsonFile);
    }

    /**
     * Charge le menu depuis le fichier JSON.
     */
    private function charger(string $jsonFile): void
    {
        if (!file_exists($jsonFile)) {
            throw new UserException("Fichier de menu introuvable : {$jsonFile}");
        }

        $json = file_get_contents($jsonFile);

        if ($json === false) {
            throw new UserException("Impossible de lire le fichier : {$jsonFile}");
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UserException('Erreur JSON : ' . json_last_error_msg());
        }

        if (!is_array($data)) {
            throw new UserException('La configuration du menu doit être un tableau JSON.');
        }

        $this->brand = $data['brand'] ?? [];
        $this->items = $data['items'] ?? [];
    }

    /**
     * Génère le menu HTML complet.
     */
    public function afficher(): string
    {
        $html = '<nav class="main-menu">';

        if (!empty($this->brand['label'])) {

            $href = $this->brand['href'] ?? null;
            $icon = $this->brand['icon'] ?? null;

            if ($href) {
                $html .= '<a class="menu-brand" href="' .
                    $this->escape($href) .
                    '">';
            } else {
                $html .= '<span class="menu-brand">';
            }

            if ($icon) {
                $html .= '<img class="menu-brand-icon" src="' .
                    $this->escape($icon) .
                    '" alt="">';
            }

            $html .= '<span class="menu-brand-label">' .
                $this->escape($this->brand['label']) .
                '</span>';

            if ($href) {
                $html .= '</a>';
            } else {
                $html .= '</span>';
            }
        }

        $html .= $this->afficherLesMenus($this->items);

        $html .= '</nav>';

        return $html;
    }

    /**
     * Génère récursivement les éléments du menu.
     */
    private function afficherLesMenus(array $items): string
    {
        $html = '<ul>';

        foreach ($items as $item) {

            if (empty($item['label'])) {
                continue;
            }

            $hasChildren = isset($item['children']) && is_array($item['children']) && count($item['children']) > 0;

            $hasHref = isset($item['href']) && trim($item['href']) !== '';

            $isActive = $this->isActive($item);

            $classes = [];

            if ($hasChildren) {
                $classes[] = 'has-submenu';
            }

            if ($isActive) {
                $classes[] = 'active';
            }

            $classAttribute = '';

            if (!empty($classes)) {
                $classAttribute = ' class="' . $this->escape(implode(' ', $classes)) . '"';
            }

            $html .= '<li' . $classAttribute . '>';

            /*
             * MENU AVEC ENFANTS
             */
            if ($hasChildren) {

                if ($hasHref) {

                    $html .= '<a href="' . $this->escape($item['href']) . '">';

                    $html .= $this->escape($item['label']);

                    $html .= '<span class="arrow">▾</span>';

                    $html .= '</a>';

                } else {

                    $html .= '<button type="button">';

                    $html .= $this->escape($item['label']);

                    $html .= '<span class="arrow">▾</span>';

                    $html .= '</button>';
                }

                $html .= '<ul class="submenu">';

                $html .= $this->afficherLesOptions(
                    $item['children']
                );

                $html .= '</ul>';

                /*
                 * MENU SIMPLE
                 */
            } elseif ($hasHref) {

                $html .= '<a href="' . $this->escape($item['href']) . '">';

                $html .= $this->escape($item['label']);

                $html .= '</a>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Génère les enfants d'un menu.
     */
    private function afficherLesOptions(array $items): string
    {
        $html = '';

        foreach ($items as $item) {

            if (empty($item['label'])) {
                continue;
            }

            $hasChildren = isset($item['children']) && is_array($item['children']) && count($item['children']) > 0;

            $hasHref = isset($item['href']) && trim($item['href']) !== '';
            $isActive = $this->isActive($item);

            $classes = [];

            if ($hasChildren) {
                $classes[] = 'has-submenu';
            }

            if ($isActive) {
                $classes[] = 'active';
            }

            $classAttribute = '';

            if (!empty($classes)) {
                $classAttribute = ' class="' . $this->escape(implode(' ', $classes)) . '"';
            }

            $html .= '<li' . $classAttribute . '>';

            if ($hasHref) {

                $html .= '<a href="' . $this->escape($item['href']) . '">';

                $html .= $this->escape($item['label']);

                if ($hasChildren) {
                    $html .= '<span class="arrow">▾</span>';
                }

                $html .= '</a>';

            } elseif ($hasChildren) {

                $html .= '<button type="button">';

                $html .= $this->escape($item['label']);

                $html .= '<span class="arrow">▾</span>';

                $html .= '</button>';
            }

            if ($hasChildren) {

                $html .= '<ul class="submenu">';

                $html .= $this->afficherLesOptions(
                    $item['children']
                );

                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }

    /**
     * Vérifie si un élément ou l'un de ses enfants
     * correspond à la page courante.
     */
    private function isActive(array $item): bool
    {
        if (isset($item['href']) && $item['href'] !== '' && $item['href'] === $this->currentPath) {
            return true;
        }

        if (isset($item['children']) && is_array($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($this->isActive($child)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Protection HTML.
     */
    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}