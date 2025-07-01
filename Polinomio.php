<?php
declare(strict_types=1);
// Clase abstracta para polinomios
abstract class PolinomioAbstracto {
    abstract public function evaluar(float $x): float;
    abstract public function derivada(): array;
}

// Clase concreta que representa un polinomio
class Polinomio extends PolinomioAbstracto {
    private array $terminos; // array asociativo: grado => coeficiente

    public function __construct(array $terminos) {
        krsort($terminos); // orden descendente por grado
        $this->terminos = $terminos;
    }

    public function evaluar(float $x): float {
        $resultado = 0.0;
        foreach ($this->terminos as $grado => $coef) {
            $resultado += $coef * pow($x, $grado);
        }
        return $resultado;
    }

    public function derivada(): array {
        $derivados = [];
        foreach ($this->terminos as $grado => $coef) {
            if ($grado > 0) {
                $derivados[$grado - 1] = $coef * $grado;
            }
        }
        return $derivados;
    }

    public function getTerminos(): array {
        return $this->terminos;
    }
}

// Función que suma dos polinomios representados como arrays asociativos
function sumarPolinomios(array $p1, array $p2): array {
    $resultado = $p1;

    foreach ($p2 as $grado => $coef) {
        if (isset($resultado[$grado])) {
            $resultado[$grado] += $coef;
        } else {
            $resultado[$grado] = $coef;
        }
    }

    // Eliminar coeficientes nulos
    foreach ($resultado as $grado => $coef) {
        if (abs($coef) < 1e-10) {
            unset($resultado[$grado]);
        }
    }

    krsort($resultado);
    return $resultado;
}

// Función para leer un polinomio desde consola
function leerPolinomio(string $nombre): array {
    echo "Ingrese el polinomio '$nombre'.\n";
    $terminos = [];
    $cantidad = (int) readline("¿Cuántos términos tiene?: ");

    for ($i = 0; $i < $cantidad; $i++) {
        $grado = (int) readline("  Grado del término #" . ($i + 1) . ": ");
        $coef = (float) readline("  Coeficiente para x^$grado: ");
        $terminos[$grado] = $coef;
    }

    return $terminos;
}

// Función para mostrar un polinomio
function mostrarPolinomio(array $p): string {
    krsort($p);
    $str = '';
    foreach ($p as $grado => $coef) {
        $term = ($coef >= 0 ? '+' : '') . $coef;
        if ($grado > 0) {
            $term .= "x";
            if ($grado > 1) $term .= "^$grado";
        }
        $str .= " $term";
    }
    return ltrim($str, ' +');
}

// Función principal
function manejarPolinomios(): void {
    echo "=== Manejo de Polinomios ===\n";

    $p1 = leerPolinomio("P1");
    $p2 = leerPolinomio("P2");

    $polinomio1 = new Polinomio($p1);
    $polinomio2 = new Polinomio($p2);

    $suma = sumarPolinomios($p1, $p2);

    echo "\nP1(x): " . mostrarPolinomio($p1) . "\n";
    echo "P2(x): " . mostrarPolinomio($p2) . "\n";
    echo "Suma:  " . mostrarPolinomio($suma) . "\n";

    $x = (float) readline("\nIngrese un valor para evaluar P1(x): ");
    echo "P1($x) = " . $polinomio1->evaluar($x) . "\n";

    $derivada = $polinomio1->derivada();
    echo "Derivada de P1: " . mostrarPolinomio($derivada) . "\n";
}

// Ejecutar
manejarPolinomios();

?>
