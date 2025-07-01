<?php

// Clase abstracta que define la estructura de un sistema de ecuaciones
abstract class SistemaEcuaciones {
    abstract public function calcularResultado();
    abstract public function validarConsistencia();
}

// Clase concreta que resuelve un sistema de ecuaciones lineales
class SistemaLineal extends SistemaEcuaciones {
    private $ec1, $ec2; 

    // Constructor que recibe dos ecuaciones y las asigna a las propiedades
    public function __construct(array $ec1, array $ec2) {
        $this->ec1 = $ec1;
        $this->ec2 = $ec2;
    }

    // Método para validar si el sistema tiene una solución consistente
    public function validarConsistencia() {
        // Extrae los coeficientes y términos independientes de las ecuaciones
        $a1 = $this->ec1['x']['coeficiente'];
        $b1 = $this->ec1['y']['coeficiente'];
        $c1 = $this->ec1['independiente'];
        $a2 = $this->ec2['x']['coeficiente'];
        $b2 = $this->ec2['y']['coeficiente'];
        $c2 = $this->ec2['independiente'];

        // Verifica si alguno de los coeficientes de 'x' o 'y' es cero, lo que haría inconsistente el sistema
        if ($a2 == 0 || $b2 == 0) return false;

        // Verifica si las ecuaciones son proporcionales pero no tienen la misma constante
        if (($a1 / $a2) == ($b1 / $b2) && ($a1 / $a2) != ($c1 / $c2)) return false;

        return true;
    }

    // Método para calcular el resultado del sistema de ecuaciones
    public function calcularResultado() {
        if (!$this->validarConsistencia()) {
            return ['error' => 'El sistema no tiene solución única.'];
        }

        // Extrae los coeficientes y términos independientes de las ecuaciones
        $a1 = $this->ec1['x']['coeficiente'];
        $b1 = $this->ec1['y']['coeficiente'];
        $c1 = $this->ec1['independiente'];
        $a2 = $this->ec2['x']['coeficiente'];
        $b2 = $this->ec2['y']['coeficiente'];
        $c2 = $this->ec2['independiente'];

        // Calcula el denominador de la fórmula para resolver el sistema
        $denominador = $a1 * $b2 - $a2 * $b1;

        // Verifica si el denominador es cero, lo que indicaría un problema (por ejemplo, ecuaciones paralelas)
        if ($denominador == 0) return ['error' => 'División por cero al resolver el sistema.'];

        // Calcula el valor de 'y' usando la fórmula para sistemas de dos ecuaciones
        $y = ($a1 * $c2 - $a2 * $c1) / $denominador;
        
        // Calcula el valor de 'x' usando el valor calculado de 'y'
        $x = ($c1 - $b1 * $y) / $a1;

        // Devuelve los resultados de 'x' y 'y'
        return ['x' => $x, 'y' => $y];
    }
}

// Función que resuelve el sistema de ecuaciones al crear una instancia de SistemaLineal
function resolverSistema($ec1, $ec2) {
    $sistema = new SistemaLineal($ec1, $ec2); 
    return $sistema->calcularResultado(); 
}

// Función para leer una ecuación desde la entrada del usuario
function leerEcuacion($num) {
    echo "Ecuación $num:\n";
    $ecuacion = [
        'x' => ['coeficiente' => (float) readline("Coeficiente de x: ")],
        'y' => ['coeficiente' => (float) readline("Coeficiente de y: ")],
        'independiente' => (float) readline("Término independiente: ")
    ];
    
    // Muestra la estructura de la ecuación ingresada
    echo "Estructura actual:\n";
    print_r($ecuacion);
    
    return $ecuacion; 
}

// Función principal que orquesta la resolución del sistema de ecuaciones
echo "=== Sistema de ecuaciones lineales ===\n";

// Llama a leer las dos ecuaciones y resuelve el sistema
$resultado = resolverSistema(leerEcuacion(1), leerEcuacion(2));

// Verifica si el resultado contiene un error
if (count($resultado) == 1) {
    echo "Error: " . $resultado['error'] . "\n"; 
} else {
    echo "x = " . $resultado['x'] . "\ny = " . $resultado['y'] . "\n";
}

?>
