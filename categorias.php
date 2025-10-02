<?php
// categorias.php
// Maneja la visualización de todas las categorías de comandos.

include 'data_handler.php'; 

// 1. Obtener la clave de la categoría desde la URL
$categoryKey = $_GET['cat'] ?? null;

// Si no hay categoría o es 'guia', redirigimos
if (!$categoryKey || $categoryKey === 'guia') {
    header('Location: index.php');
    exit;
}

// 2. Obtener el contenido y el título
$content = getCategoryContent($categoryKey);
$pageTitle = getCategoryTitle($categoryKey);

// Si no se encuentra el contenido, mostramos error
if (!$content) {
    $pageTitle = "Error 404";
    $errorMessage = "La categoría de comandos solicitada ('{$categoryKey}') no se encontró.";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LINUX | <?php echo htmlspecialchars($pageTitle); ?></title>
    
    <link rel="shortcut icon" href="assets/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <?php include 'menu.php'; ?>

    <div id="wrapper">
        <?php if (isset($errorMessage)): ?>
            <h1 class="preview-title" style="text-align: center; color: red;">Error</h1>
            <p style="text-align: center;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php else: ?>
            <h1 class="preview-title" style="font-size: 2rem; padding-top:15px; padding-bottom: 15px; text-align: center;">
                🐧 <?php echo htmlspecialchars($pageTitle); ?>
            </h1>

            <?php
            // USAR LA FUNCIÓN generateTableContent() QUE DETECTA LA ESTRUCTURA
            echo generateTableContent($categoryKey);
            ?>
        <?php endif; ?>
    </div>

    <script src="assets/main.js"></script>
</body>
</html>
