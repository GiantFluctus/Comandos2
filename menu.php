<?php
// menu.php

// Asegúrate de incluir data_handler.php si no está incluido globalmente.
include_once 'data_handler.php'; 

// Función para obtener los items del menú (usa funciones definidas en data_handler.php)
function getMenuItems() {
    $keys = getCategoryKeys();
    $items = [];
    foreach ($keys as $key) {
        $items[$key] = getCategoryTitle($key);
    }
    return $items;
}

$menuItems = getMenuItems();

?>
<nav class="p-navigation">
    <div class="p-navigation__row">
        
        <!-- Título principal siempre enlaza a la guía -->
        <!-- Se añade p-navigation__link para asegurar que reciba el estilo básico de enlace blanco -->
        <a href="index.php" class="p-navigation__brand p-navigation__link">
            🐧 Comandos + Usados | Linux
        </a>

        <!-- Ícono de hamburguesa para vistas móviles -->
        <div class="p-navigation__hamburger" id="hamburger">&#9776;</div>
        
        <!-- p-navigation__items: Contenedor principal de los enlaces, coincide con tu CSS -->
        <div class="p-navigation__items nav-links">
            <!-- Enlace a la guía (index.php) -->
            <!-- nav-link es usado por main.js para la navegación dinámica -->
            <a href="index.php" class="p-navigation__link nav-link" data-category="guia">Guía de Uso</a>

            <?php
            // Generación dinámica de los enlaces de categoría
            foreach ($menuItems as $key => $title) {
                // El href es la URL limpia que el usuario verá
                $href = htmlspecialchars($key);
                $displayTitle = htmlspecialchars($title);
                $categoryKey = htmlspecialchars($key);
                
                // p-navigation__link es la clase de estilo, nav-link para la lógica JS
                echo "<a href=\"./{$href}\" class=\"p-navigation__link nav-link\" data-category=\"{$categoryKey}\">{$displayTitle}</a>";
            }
            ?>
        </div>
        
    </div>
</nav>

