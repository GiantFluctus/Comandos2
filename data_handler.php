<?php
// data_handler.php
// Contiene las funciones para cargar, procesar y acceder a los datos de comandos.

// --- 1. CONFIGURACIÓN Y CARGA DE DATOS CENTRALIZADA ---
$jsonFilePath = 'assets/comandos.json';
$allData = null;
$loadError = null;

/**
 * Carga el archivo JSON una sola vez y maneja los errores.
 */
function loadData() {
    global $jsonFilePath, $allData, $loadError;

    if ($allData !== null || $loadError !== null) {
        return $allData;
    }

    if (!file_exists($jsonFilePath)) {
        $loadError = "ERROR FATAL: Archivo de datos no encontrado en: " . htmlspecialchars($jsonFilePath);
        return null;
    }

    $jsonContent = file_get_contents($jsonFilePath);
    $data = json_decode($jsonContent, true);
    $errorCode = json_last_error();

    if ($errorCode !== JSON_ERROR_NONE) {
        $errorMsg = match ($errorCode) {
            JSON_ERROR_DEPTH => 'Exceso de anidamiento (profundidad).',
            JSON_ERROR_STATE_MISMATCH => 'JSON con formato incorrecto o desordenado.',
            JSON_ERROR_CTRL_CHAR => 'Carácter de control inesperado encontrado.',
            JSON_ERROR_SYNTAX => 'Error de sintaxis (coma extra, comillas simples, etc.).',
            JSON_ERROR_UTF8 => 'Caracteres UTF-8 malformados.',
            default => 'Error de decodificación de JSON desconocido (' . $errorCode . ')',
        };
        $loadError = "ERROR DE JSON: Falló la decodificación. Razón: <strong>{$errorMsg}</strong>.";
        return null;
    }

    $allData = $data;
    return $allData;
}

// Cargar los datos al inicio del script
loadData();


// --- 2. FUNCIONES REQUERIDAS POR menu.php ---

/**
 * Retorna las claves (slugs) de todas las categorías disponibles.
 */
function getCategoryKeys(): array {
    global $allData;
    if ($allData) {
        $keys = array_keys($allData);
        // Filtrar claves especiales como 'titles'
        return array_filter($keys, fn($key) => $key !== 'titles');
    }
    return [];
}

/**
 * Retorna el título legible de una categoría.
 */
function getCategoryTitle(string $key): string {
    global $allData;

    // 1. Manejar la clave especial 'guia'
    if ($key === 'guia' || $key === 'index.php') {
        return "Guía de Uso";
    }

    // 2. Intentar buscar en la sección 'titles' del JSON (si existe)
    if ($allData && isset($allData['titles'][$key])) {
        return $allData['titles'][$key];
    }
    
    // 3. Fallback: Capitalizar la clave
    return ucwords(str_replace('-', ' ', $key));
}


// --- 3. FUNCIONES REQUERIDAS POR categorias.php ---

/**
 * Retorna el contenido de una categoría específica.
 */
function getCategoryContent(string $categoryKey): ?array {
    global $allData, $loadError;

    if ($loadError) {
        return null; 
    }

    if ($allData && isset($allData[$categoryKey]) && $categoryKey !== 'titles') {
        return $allData[$categoryKey];
    }

    return null;
}

/**
 * Genera una tabla HTML a partir de un array de comandos.
 * Incluye los botones "Copiar"
 */
function renderCommandsTable(array $commands): string {
    $html = '<div class="category-table-container">
    <table class="style1 tabla-generada">
        <thead>
            <tr>
                <th>N°</th>
                <th>Descripción</th>
                <th>Comando</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($commands as $index => $item) {
        if (!isset($item['descripcion']) || !isset($item['comando'])) continue;

        $numero = $index + 1; 
        $descripcion = htmlspecialchars($item['descripcion']);
        $comando = htmlspecialchars($item['comando']);
        
        $html .= "
            <tr>
                <td>{$numero}</td>
                <td>{$descripcion}</td>
                <td>
                    <div class='code-snippet'>
                        {$comando}
                        <button class='copy-btn'>Copiar</button>
                    </div>
                </td>
            </tr>";
    }

    $html .= '
        </tbody>
    </table>
    </div>';

    return $html;
}


/**
 * Genera el HTML de las tablas de comandos para una categoría específica.
 * DETECTA AUTOMÁTICAMENTE SI ES ESTRUCTURA ANIDADA O SIMPLE
 */
function generateTableContent(string $categoryKey): string {
    global $allData, $loadError;

    if ($loadError) {
        return "<div style='text-align:center; padding: 20px; color: red;'><strong>ERROR DE DATOS GLOBAL:</strong> {$loadError}</div>";
    }

    if (!isset($allData[$categoryKey])) {
        return "<div style='text-align:center; padding: 20px; color: orange;'>
            ADVERTENCIA: Categoría <strong>'" . htmlspecialchars($categoryKey) . "'</strong> no encontrada en el archivo JSON.
        </div>";
    }

    $categoryData = $allData[$categoryKey];
    $outputHtml = '';

    // --- DETECCIÓN MEJORADA DE ESTRUCTURA ---
    
    if (!is_array($categoryData) || empty($categoryData)) {
        return "<p style='color: red;'>ERROR: La categoría no contiene datos válidos.</p>";
    }

    // Obtener el primer elemento para analizar la estructura
    $firstElement = reset($categoryData);
    
    // CRITERIO DE DETECCIÓN:
    // Si el primer elemento tiene 'descripcion' y 'comando', es ESTRUCTURA SIMPLE
    // Si el primer elemento es un array SIN 'descripcion', es ESTRUCTURA ANIDADA
    
    if (is_array($firstElement) && isset($firstElement['descripcion']) && isset($firstElement['comando'])) {
        // ESTRUCTURA SIMPLE (como sistema, maria, redes, etc.)
        $outputHtml .= renderCommandsTable($categoryData);
        
    } elseif (is_array($firstElement)) {
        // ESTRUCTURA ANIDADA (como kernel)
        foreach ($categoryData as $subCategoryTitle => $commandsArray) {
            $outputHtml .= "<h2 class='preview-title'>› " . htmlspecialchars($subCategoryTitle) . "</h2>"; 
            
            if (is_array($commandsArray)) {
                $outputHtml .= renderCommandsTable($commandsArray);
            } else {
                $outputHtml .= "<p style='color: red;'>Error: Los comandos de la subcategoría '" . htmlspecialchars($subCategoryTitle) . "' no son un array.</p>";
            }
        }
    } else {
        $outputHtml = "<p style='color: red;'>ERROR: Formato de datos no reconocido para la categoría: " . htmlspecialchars($categoryKey) . "</p>";
    }

    return $outputHtml;
}
?>
