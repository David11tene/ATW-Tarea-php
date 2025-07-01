<?php
declare(strict_types=1);

// Clase abstracta para polinomios
abstract class PolinomioAbstracto {
    abstract public function evaluar(float $x): float;
    abstract public function derivada(): array;
}

// Clase concreta que representa un polinomio
class Polinomio extends PolinomioAbstracto {
    private array $terminos; 

    // Constructor que toma los términos del polinomio
    public function __construct(array $terminos) {
        krsort($terminos); // Ordena los términos de mayor a menor grado
        $this->terminos = $terminos;
    }

    // Método para evaluar el polinomio en un valor x
    public function evaluar(float $x): float {
        $resultado = 0.0;

        // Calcula el valor del polinomio sumando los términos
        foreach ($this->terminos as $grado => $coef) {
            $resultado += $coef * pow($x, $grado); // coef * x^grado
        }

        return $resultado;
    }

    // Método para calcular la derivada del polinomio
    public function derivada(): array {
        $derivados = [];

        // Calcula la derivada de cada término
        foreach ($this->terminos as $grado => $coef) {
            if ($grado > 0) {  // La derivada de x^0 es 0, por lo que se ignora
                $derivados[$grado - 1] = $coef * $grado; // Derivada: coef * grado
            }
        }

        return $derivados;
    }

    // Método para obtener los términos del polinomio
    public function getTerminos(): array {
        return $this->terminos;
    }
}

// Función que suma dos polinomios representados como arrays asociativos
function sumarPolinomios(array $p1, array $p2): array {
    $resultado = $p1;

    // Suma los coeficientes de los términos con el mismo grado
    foreach ($p2 as $grado => $coef) {
        if (isset($resultado[$grado])) {
            $resultado[$grado] += $coef; 
        } else {
            $resultado[$grado] = $coef; 
        }
    }

    // Eliminar coeficientes nulos (aproximados a 0)
    foreach ($resultado as $grado => $coef) {
        if (abs($coef) < 1e-10) {
            unset($resultado[$grado]); // Elimina términos que tienen coeficiente 0
        }
    }

    krsort($resultado); // Ordena los términos de mayor a menor grado
    return $resultado;
}

// Función para leer un polinomio desde la consola
function leerPolinomio(string $nombre): array {
    echo "Ingrese el polinomio '$nombre'.\n";
    $terminos = [];
    $cantidad = (int) readline("¿Cuántos términos tiene?: "); 

    // Lee los términos del polinomio
    for ($i = 0; $i < $cantidad; $i++) {
        $grado = (int) readline("  Grado del término #" . ($i + 1) . ": "); 
        $coef = (float) readline("  Coeficiente para x^$grado: "); 
        $terminos[$grado] = $coef; // Almacena el grado y el coeficiente
    }

    return $terminos;
}

// Función para mostrar un polinomio de manera legible
function mostrarPolinomio(array $p): string {
    krsort($p); // Ordena los términos del polinomio de mayor a menor grado
    $str = '';

    foreach ($p as $grado => $coef) {
        $term = ($coef >= 0 ? '+' : '') . $coef; // Añade el signo '+' si el coeficiente es positivo

        if ($grado > 0) {
            $term .= "x";
            if ($grado > 1) $term .= "^$grado"; 
        }
        $str .= " $term"; 
    }

    return ltrim($str, ' +'); // Elimina el espacio inicial y el signo '+' si es necesario
}

// Función principal para manejar polinomios
function manejarPolinomios(): void {
    echo "=== Manejo de Polinomios ===\n";

    // Lee los polinomios P1 y P2 desde la consola
    $p1 = leerPolinomio("P1");
    $p2 = leerPolinomio("P2");

    // Crea instancias de la clase Polinomio con los polinomios leídos
    $polinomio1 = new Polinomio($p1);
    $polinomio2 = new Polinomio($p2);

    // Suma los dos polinomios
    $suma = sumarPolinomios($p1, $p2);

    // Muestra los polinomios y su suma
    echo "\nP1(x): " . mostrarPolinomio($p1) . "\n";
    echo "P2(x): " . mostrarPolinomio($p2) . "\n";
    echo "Suma:  " . mostrarPolinomio($suma) . "\n";

    // Lee un valor de x para evaluar P1(x)
    $x = (float) readline("\nIngrese un valor para evaluar P1(x): ");
    echo "P1($x) = " . $polinomio1->evaluar($x) . "\n";

    // Calcula y muestra la derivada de P1
    $derivada = $polinomio1->derivada();
    echo "Derivada de P1: " . mostrarPolinomio($derivada) . "\n";

    // Lee un valor de x para evaluar P2(x)
    $x = (float) readline("Ingrese un valor para evaluar P2(x): ");
    echo "P2($x) = " . $polinomio2->evaluar($x) . "\n";

    // Calcula y muestra la derivada de P2
    $derivadaP2 = $polinomio2->derivada();
    echo "Derivada de P2: " . mostrarPolinomio($derivadaP2) . "\n";
}

// Ejecutar la función principal
manejarPolinomios();
