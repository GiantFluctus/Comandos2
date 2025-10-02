<?php
// debug.php
// Script para verificar la estructura del JSON

header('Content-Type: text/html; charset=utf-8');

$jsonFile = 'assets/comandos.json';

if (!file_exists($jsonFile)) {
    die("ERROR: No se encuentra el archivo {$jsonFile}");
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("ERROR DE JSON: " . json_last_error_msg());
}

echo "<html><head><meta charset='UTF-8'><style>
body { font-family: monospace; padding: 20px; background: #f5f5f5; }
.category { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #77216F; }
.type { color: #77216F; font-weight: bold; }
.estructura-simple { color: green; }
.estructura-anidada { color: blue; }
pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
</style></head><body>";

echo "<h1>🔍 Análisis de Estructura del JSON</h1>";

foreach ($data as $categoryKey => $categoryData) {
    if ($categoryKey === 'titles') {
        echo "<div class='category'>";
        echo "<h2>📋 {$categoryKey} (metadatos - ignorar)</h2>";
        echo "</div>";
        continue;
    }
    
    echo "<div class='category'>";
    echo "<h2>📁 {$categoryKey}</h2>";
    
    if (!is_array($categoryData) || empty($categoryData)) {
        echo "<p class='type'>⚠️ NO ES UN ARRAY VÁLIDO</p>";
        continue;
    }
    
    // Obtener el primer elemento
    $firstElement = reset($categoryData);
    $firstKey = key($categoryData);
    
    // Detectar estructura
    if (is_array($firstElement) && isset($firstElement['descripcion']) && isset($firstElement['comando'])) {
        echo "<p class='type estructura-simple'>✅ ESTRUCTURA SIMPLE (array de comandos)</p>";
        echo "<p>Total de comandos: " . count($categoryData) . "</p>";
        echo "<p>Primer comando:</p>";
        echo "<pre>" . htmlspecialchars(print_r($firstElement, true)) . "</pre>";
        
    } elseif (is_array($firstElement)) {
        echo "<p class='type estructura-anidada'>✅ ESTRUCTURA ANIDADA (con subcategorías)</p>";
        echo "<p>Total de subcategorías: " . count($categoryData) . "</p>";
        echo "<p>Primera subcategoría: <strong>{$firstKey}</strong></p>";
        echo "<p>Comandos en primera subcategoría: " . count($firstElement) . "</p>";
        
        if (!empty($firstElement)) {
            $firstCommand = reset($firstElement);
            echo "<p>Primer comando de la subcategoría:</p>";
            echo "<pre>" . htmlspecialchars(print_r($firstCommand, true)) . "</pre>";
        }
        
    } else {
        echo "<p class='type'>❌ ESTRUCTURA NO RECONOCIDA</p>";
        echo "<p>Tipo del primer elemento: " . gettype($firstElement) . "</p>";
        echo "<pre>" . htmlspecialchars(print_r($firstElement, true)) . "</pre>";
    }
    
    echo "</div>";
}

echo "</body></html>";
?>