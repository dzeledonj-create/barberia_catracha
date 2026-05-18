<?php
// generar_diagrama.php

$dir = __DIR__;
$archivos = glob("$dir/*.php");

$mermaid = "```mermaid\nclassDiagram\n";
$relaciones = [];
$clasesEncontradas = [];

foreach ($archivos as $archivo) {
    if (basename($archivo) === 'generar_diagrama.php') continue;
    
    $contenido = file_get_contents($archivo);
    $nombreClase = pathinfo($archivo, PATHINFO_FILENAME);
    
    // Validar si el archivo contiene una clase de verdad
    if (!preg_match('/class\s+(\w+)/', $contenido)) continue;
    $clasesEncontradas[] = $nombreClase;
    
    $mermaid .= "    class $nombreClase {\n";
    
    // Detectar Herencia (extends)
    if (preg_match('/class\s+\w+\s+extends\s+(\w+)/', $contenido, $matchesHereda)) {
        $relaciones[] = "    {$matchesHereda[1]} <|-- $nombreClase";
    }
    
    // Procesar línea por línea para propiedades y métodos
    $lineas = explode("\n", $contenido);
    foreach ($lineas as $linea) {
        $linea = trim($linea);
        
        // 1. Detectar Atributos (ej: public string $email;)
        if (preg_match('/(public|private|protected)\s+(\??\w+)?\s+\$(\w+)/', $linea, $mProp)) {
            $vis = ($mProp[1] == 'public') ? '+' : (($mProp[1] == 'private') ? '-' : '#');
            $tipo = $mProp[2] ? $mProp[2] . ' ' : '';
            $mermaid .= "        $vis$tipo{$mProp[3]}\n";
        }
        
        // 2. Detectar Métodos (ej: public function getNombre(): string)
        if (preg_match('/(public|private|protected)\s+(static\s+)?function\s+(\w+)\((.*?)\)/', $linea, $mMet)) {
            $vis = ($mMet[1] == 'public') ? '+' : (($mMet[1] == 'private') ? '-' : '#');
            $nombreMetodo = $mMet[3];
            $params = $mMet[4];
            
            // Limpiar signos de '$' en los parámetros para el diagrama
            $paramsLimpios = str_replace('$', '', preg_replace('/[a-zA-Z0-9_?|\s]+\$/', '$', $params));
            
            // Detectar tipo de retorno si lo tiene declarado
            $tipoRetorno = '';
            if (preg_match('/\)\s*:\s*(\??\w+)/', $linea, $mRet)) {
                $tipoRetorno = ' : ' . $mRet[1];
            }
            
            $mermaid .= "        $vis$nombreMetodo($paramsLimpios)$tipoRetorno\n";
        }
    }
    $mermaid .= "    }\n\n";
}

// Unir relaciones de herencia
foreach ($relaciones as $rel) {
    $mermaid .= $rel . "\n";
}

// Relaciones de dependencia fijas/arquitectura
if (in_array('GestorUsuarios', $clasesEncontradas)) {
    $mermaid .= "    GestorUsuarios ..> Usuario : Maneja\n";
    $mermaid .= "    GestorUsuarios ..> BD : Obtiene conexion\n";
}
if (in_array('Usuario', $clasesEncontradas)) {
    $mermaid .= "    Usuario ..> BD : Obtiene conexion\n";
}

$mermaid .= "```";

// Guardar/Actualizar el archivo Markdown automáticamente
file_put_contents("$dir/diagramas.md", "# Diagrama de Clases (Autogenerado)\n\n" . $mermaid);
echo "¡Diagrama actualizado con éxito!\n";