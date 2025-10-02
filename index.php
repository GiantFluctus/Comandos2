<?php
// index.php
// Página de inicio que muestra la guía de uso.
// Incluimos data_handler para asegurar que las funciones estén disponibles para menu.php
include_once 'data_handler.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LINUX | Guía de Comandos más Usados</title>
        
    <link rel="shortcut icon" href="assets/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <!-- Incluye tu archivo de estilos principal -->
    <link type="text/css" rel="stylesheet" href="assets/styles.css">

</head>

<body>
    <?php // Incluimos el menú de navegación
    include 'menu.php'; 
    ?>

    <div id="wrapper">
        <h1 class="preview-title" style="text-align: center; font-size: 2rem;">
            Bienvenido a la Guía de Comandos Linux
        </h1>

        <div class="guia-section">
            <h2>🚀 Cómo usar esta aplicación</h2>
            <p>Esta es una guía de referencia rápida para los comandos más comunes de Linux, organizados por categoría. Úsala como tu "hoja de trucos" personal.</p>
            <ul>
                <li>Usa el menú superior para navegar entre las categorías.</li>
                <li>La navegación es **instantánea** (sin recarga de página) gracias a JavaScript.</li>
            </ul>
        </div>

        <div class="guia-section">
            <h2>🖱️ Copiar Comandos al Instante</h2>
            <p>Todos los comandos están listos para ser copiados. Hay dos formas de hacerlo:</p>
            <ol>
                <li>Haz clic en el botón **"Copiar"** al lado del comando.</li>
                <li>**Haz clic en cualquier parte del bloque de código** completo. ¡Es más rápido!</li>
            </ol>
            <div class="code-snippet">
                ls -lah
            </div>
            <p class="copy-info">^ Haz clic en el texto de arriba o en el botón "Copiar" para probarlo. ^</p>
        </div>

        <div class="guia-section">
            <h2>🌐 URLs Limpias</h2>
            <p>Aunque no tengas acceso a la configuración de Nginx, esta aplicación te da URLs limpias (ej. `/kernel`) gracias a la **History API** de JavaScript. Esto te permite compartir la URL de una categoría específica sin exponer el nombre del archivo PHP.</p>
        </div>
    </div>

    <script src="assets/main.js"></script>
</body>
</html>

