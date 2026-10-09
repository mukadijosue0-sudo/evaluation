<?php
declare(strict_types=1);

use ClasseTechnique\Menu;

$menu = new Menu(__DIR__ . '/../config/menu.json');

?>
    <link rel="stylesheet" href="/css/menu.css">
<?= $menu->afficher() ?>