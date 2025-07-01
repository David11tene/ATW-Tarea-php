<?php

abstract class SistemaEcuaciones {
    abstract public function calcularResultado();
    abstract public function validarConsistencia();
}

class SistemaLineal extends SistemaEcuaciones {
    private $ec1, $ec2;

    public function __construct(array $ec1, array $ec2) {
        $this->ec1 = $ec1;
        $this->ec2 = $ec2;
    }

    public function validarConsistencia() {
        $a1 = $this->ec1['x']['coeficiente'];
        $b1 = $this->ec1['y']['coeficiente'];
        $c1 = $this->ec1['independiente'];
        $a2 = $this->ec2['x']['coeficiente'];
        $b2 = $this->ec2['y']['coeficiente'];
        $c2 = $this->ec2['independiente'];

        if ($a2 == 0 || $b2 == 0) return false;
        if (($a1 / $a2) == ($b1 / $b2) && ($a1 / $a2) != ($c1 / $c2)) return false;
        return true;
    }

    public function calcularResultado() {
        if (!$this->validarConsistencia()) {
            return ['error' => 'El sistema no tiene solución única.'];
        }

        $a1 = $this->ec1['x']['coeficiente'];
        $b1 = $this->ec1['y']['coeficiente'];
        $c1 = $this->ec1['independiente'];
        $a2 = $this->ec2['x']['coeficiente'];
        $b2 = $this->ec2['y']['coeficiente'];
        $c2 = $this->ec2['independiente'];

        $denominador = $a1 * $b2 - $a2 * $b1;
        if ($denominador == 0) return ['error' => 'División por cero al resolver el sistema.'];

        $y = ($a1 * $c2 - $a2 * $c1) / $denominador;
        $x = ($c1 - $b1 * $y) / $a1;

        return ['x' => $x, 'y' => $y];
    }
}

function resolverSistema($ec1, $ec2) {
    $sistema = new SistemaLineal($ec1, $ec2);
    return $sistema->calcularResultado();
}

function leerEcuacion($num) {
    echo "Ecuación $num:\n";
    $ecuacion = [
        'x' => ['coeficiente' => (float) readline("Coeficiente de x: ")],
        'y' => ['coeficiente' => (float) readline("Coeficiente de y: ")],
        'independiente' => (float) readline("Término independiente: ")
    ];
    
    echo "Estructura actual:\n";
    print_r($ecuacion);
    
    return $ecuacion;
}

echo "=== Sistema de ecuaciones lineales ===\n";
$resultado = resolverSistema(leerEcuacion(1), leerEcuacion(2));

if (count($resultado) == 1) {
    echo "Error: " . $resultado['error'] . "\n";
} else {
    echo "x = " . $resultado['x'] . "\ny = " . $resultado['y'] . "\n";
}

?>
