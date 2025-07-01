<?php

abstract class Estadistica {
    abstract public function calcularMedia($datos);
    abstract public function calcularMediana($datos);
    abstract public function calcularModa($datos);
}

class EstadisticaBasica extends Estadistica {
    
    public function calcularMedia($datos) {
        return count($datos) === 0 ? null : array_sum($datos) / count($datos);
    }


    public function calcularMediana($datos) {
        if (count($datos) === 0) return null;
        
        sort($datos);
        $n = count($datos);
        
        return $n % 2 === 0 ? 
            ($datos[$n/2 - 1] + $datos[$n/2]) / 2 : 
            $datos[($n-1)/2];
    }

    public function calcularModa($datos) {
        if (count($datos) === 0) return [];
        
        $frecuencias = array_count_values($datos);
        $maxRepeticiones = max($frecuencias);
        
        if ($maxRepeticiones === 1) return [];
        
        $moda = [];
        foreach ($frecuencias as $numero => $repeticiones) {
            if ($repeticiones === $maxRepeticiones) {
                $moda[] = $numero;
            }
        }
        
        return $moda;
    }

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

function leerDatos() {
    $conjuntos = [];
    $cantidad = (int) readline("¿Cuántos conjuntos?: ");

    for ($i = 1; $i <= $cantidad; $i++) {
        $clave = "conjunto" . $i;
        $entrada = readline("Números del conjunto #$i (separados por espacios): ");
        
        $numeros = [];
        foreach (explode(" ", $entrada) as $valor) {
            if (trim($valor) !== "") {
                $numeros[] = (int) $valor;
            }
        }
        
        $conjuntos[$clave] = $numeros;
    }

    return $conjuntos;
}

$conjuntos = leerDatos();

echo "\n--- Estructura de datos ingresados ---\n";
print_r($conjuntos);

$estadistica = new EstadisticaBasica();
$informe = $estadistica->generarInforme($conjuntos);

echo "\n--- Estructura del informe generado ---\n";
print_r($informe);

echo "\n--- Resultados ---\n";
foreach ($informe as $clave => $datos) {
    echo "Conjunto: $clave\n";
    echo "  Media: " . ($datos['media'] ? round($datos['media'], 2) : "N/A") . "\n";
    echo "  Mediana: " . ($datos['mediana'] ?: "N/A") . "\n";
    echo "  Moda: " . (empty($datos['moda']) ? "No hay" : implode(", ", $datos['moda'])) . "\n\n";
}

