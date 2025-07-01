<?php

// Clase abstracta que define los métodos para calcular estadísticas
abstract class Estadistica {
    abstract public function calcularMedia($datos);
    abstract public function calcularMediana($datos);
    abstract public function calcularModa($datos);
}

// Clase concreta que implementa los métodos de la clase abstracta
class EstadisticaBasica extends Estadistica {
    
    // Método para calcular la media de un conjunto de datos
    public function calcularMedia($datos) {
        return count($datos) === 0 ? null : array_sum($datos) / count($datos);
    }

    // Método para calcular la mediana de un conjunto de datos
    public function calcularMediana($datos) {
        if (count($datos) === 0) return null;
        
        sort($datos); // Ordena los datos de menor a mayor
        $n = count($datos); // Obtiene la cantidad de elementos
        
        // Si el número de elementos es par, se promedia el par de valores centrales
        // Si es impar, se toma el valor central
        return $n % 2 === 0 ? 
            ($datos[$n/2 - 1] + $datos[$n/2]) / 2 : 
            $datos[($n-1)/2];
    }

    // Método para calcular la moda de un conjunto de datos
    public function calcularModa($datos) {
        if (count($datos) === 0) return [];
        
        // Cuenta las repeticiones de cada número en el array
        $frecuencias = array_count_values($datos);
        // Encuentra la máxima cantidad de repeticiones
        $maxRepeticiones = max($frecuencias);
        
        // Si la máxima repetición es 1, significa que no hay moda (todos los valores son únicos)
        if ($maxRepeticiones === 1) return [];
        
        $moda = [];
        // Recorre las frecuencias y agrega los números con la máxima repetición al array moda
        foreach ($frecuencias as $numero => $repeticiones) {
            if ($repeticiones === $maxRepeticiones) {
                $moda[] = $numero;
            }
        }
        
        return $moda;
    }

    // Método que genera un informe con las estadísticas de varios conjuntos de datos
    public function generarInforme($conjuntos) {
        $informe = [];
        
        foreach ($conjuntos as $clave => $datos) {
            $informe[$clave] = [
                'media' => $this->calcularMedia($datos),  
                'mediana' => $this->calcularMediana($datos), 
                'moda' => $this->calcularModa($datos)      
            ];
        }
        
        return $informe; 
    }
}

// Función que lee los datos ingresados por el usuario
function leerDatos() {
    $conjuntos = [];
    $cantidad = (int) readline("¿Cuántos conjuntos?: "); 

    // Recorre los conjuntos y solicita los números para cada uno
    for ($i = 1; $i <= $cantidad; $i++) {
        $clave = "conjunto" . $i; 
        $entrada = readline("Números del conjunto #$i (separados por espacios): "); 
        
        $numeros = [];
        // Convierte la entrada en un array de números enteros
        foreach (explode(" ", $entrada) as $valor) {
            if (trim($valor) !== "") { // Ignora los valores vacíos
                $numeros[] = (int) $valor; // Convierte cada valor a un entero y lo agrega al array
            }
        }
        
        $conjuntos[$clave] = $numeros; // Asocia el conjunto de datos al array
    }

    return $conjuntos; 
}

$conjuntos = leerDatos();

// Muestra la estructura de los datos ingresados
echo "\n--- Estructura de datos ingresados ---\n";
print_r($conjuntos);

// Crea una instancia de la clase EstadisticaBasica y genera el informe
$estadistica = new EstadisticaBasica();
$informe = $estadistica->generarInforme($conjuntos);

// Muestra la estructura del informe generado
echo "\n--- Estructura del informe generado ---\n";
print_r($informe);

// Muestra los resultados del informe
echo "\n--- Resultados ---\n";
foreach ($informe as $clave => $datos) {
    echo "Conjunto: $clave\n";
    echo "  Media: " . ($datos['media'] ? round($datos['media'], 2) : "N/A") . "\n";
    echo "  Mediana: " . ($datos['mediana'] ?: "N/A") . "\n";
    echo "  Moda: " . (empty($datos['moda']) ? "No hay" : implode(", ", $datos['moda'])) . "\n\n";
}
