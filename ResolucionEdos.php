<?php

// Clase abstracta para ecuaciones diferenciales
abstract class EcuacionDiferencial {
    protected float $x0;
    protected float $y0;
    protected float $h;
    protected float $xFinal;
    protected array $solucion;
    
    public function __construct(float $x0, float $y0, float $h, float $xFinal) {
        $this->x0 = $x0;
        $this->y0 = $y0;
        $this->h = $h;
        $this->xFinal = $xFinal;
        $this->solucion = [];
    }
    
    abstract public function resolverEuler(array $condicionesIniciales, array $parametros): array;
    
    // Cambiar a public para poder acceder desde fuera de la clase
    public function mostrarResultados(): void {
        echo "Resultados de la solución:\n";
        echo "========================\n";
        printf("%-10s %-15s\n", "x", "y");
        echo "------------------------\n";
        
        foreach ($this->solucion as $x => $y) {
            printf("%-10s %-15.6f\n", $x, $y);
        }
        echo "\n";
    }
}

// Implementación concreta del método de Euler
class EulerNumerico extends EcuacionDiferencial {
    protected string $tipoEcuacion;
    
    public function __construct(float $x0, float $y0, float $h, float $xFinal, string $tipoEcuacion) {
        parent::__construct($x0, $y0, $h, $xFinal);
        $this->tipoEcuacion = $tipoEcuacion;
    }
    
    public function resolverEuler(array $condicionesIniciales, array $parametros): array {
        // Extraer parámetros del método
        $x0 = (float)$condicionesIniciales['x0'];
        $y0 = (float)$condicionesIniciales['y0'];
        $h = (float)$parametros['h'];
        $xFinal = (float)$parametros['x_final'];
        
        // Array asociativo para almacenar la solución
        $this->solucion = [];
        
        // Valores iniciales
        $x = $x0;
        $y = $y0;
        $this->solucion[(string)$x] = $y;
        
        // Aplicar método de Euler iterativamente
        while ($x < $xFinal) {
            // Calcular la derivada según el tipo de ecuación
            $dydx = $this->calcularDerivada($x, $y);
            
            // Aplicar fórmula de Euler: y_{n+1} = y_n + h * f(x_n, y_n)
            $y = $y + $h * $dydx;
            $x = $x + $h;
            
            // Almacenar el resultado con clave formateada
            $clave = number_format($x, 2);
            $this->solucion[$clave] = $y;
        }
        
        return $this->solucion;
    }
    
    private function calcularDerivada(float $x, float $y): float {
        switch ($this->tipoEcuacion) {
            case 'lineal':
                // dy/dx = x + y
                return $x + $y;
                
            case 'exponencial':
                // dy/dx = -2 * y
                return -2.0 * $y;
                
            case 'cuadratica':
                // dy/dx = x^2 + y
                return ($x * $x) + $y;
                
            case 'seno':
                // dy/dx = sin(x) + y
                return sin($x) + $y;
                
            default:
                // Por defecto: dy/dx = x + y
                return $x + $y;
        }
    }
    
    public function mostrarInformacion(): void {
        echo "Información de la Ecuación Diferencial\n";
        echo "=====================================\n";
        echo "Tipo de ecuación: " . $this->tipoEcuacion . "\n";
        echo "Condiciones iniciales: x0 = " . $this->x0 . ", y0 = " . $this->y0 . "\n";
        echo "Paso h = " . $this->h . "\n";
        echo "Intervalo: [" . $this->x0 . ", " . $this->xFinal . "]\n\n";
    }
}

// Función principal que aplica el método
function aplicarMetodo(array $condicionesIniciales, array $parametros, string $tipoEcuacion): array {
    $euler = new EulerNumerico(
        $condicionesIniciales['x0'],
        $condicionesIniciales['y0'],
        $parametros['h'],
        $parametros['x_final'],
        $tipoEcuacion
    );
    
    $euler->mostrarInformacion();
    $solucion = $euler->resolverEuler($condicionesIniciales, $parametros);
    $euler->mostrarResultados(); // Ahora es público, no hay error
    
    return $solucion;
}

// Programa principal - Consola
echo "=== RESOLUCIÓN DE ECUACIONES DIFERENCIALES - MÉTODO DE EULER ===\n\n";

// Ejemplo 1: Ecuación lineal dy/dx = x + y
echo "EJEMPLO 1: Ecuación Lineal\n";
echo "Ecuación: dy/dx = x + y\n";
echo str_repeat("-", 50) . "\n";

// Condiciones iniciales
$condicionesIniciales = [
    'x0' => 0.0,  
    'y0' => 1.0   
];

// Parámetros del método
$parametros = [
    'h' => 0.1,      // tamaño del paso
    'x_final' => 1.0  // valor final de x
];

// Resolver la ecuación diferencial
$resultado = aplicarMetodo($condicionesIniciales, $parametros, 'lineal');

echo str_repeat("=", 60) . "\n\n";

// Ejemplo 2: Ecuación exponencial dy/dx = -2*y
echo "EJEMPLO 2: Ecuación Exponencial\n";
echo "Ecuación: dy/dx = -2*y\n";
echo str_repeat("-", 50) . "\n";

$condicionesIniciales2 = [
    'x0' => 0.0,
    'y0' => 2.0
];

$parametros2 = [
    'h' => 0.2,
    'x_final' => 2.0
];

$resultado2 = aplicarMetodo($condicionesIniciales2, $parametros2, 'exponencial');

echo str_repeat("=", 60) . "\n\n";

// Ejemplo 3: Ecuación cuadrática dy/dx = x^2 + y
echo "EJEMPLO 3: Ecuación Cuadrática\n";
echo "Ecuación: dy/dx = x^2 + y\n";
echo str_repeat("-", 50) . "\n";

$condicionesIniciales3 = [
    'x0' => 0.0,
    'y0' => 0.5
];

$parametros3 = [
    'h' => 0.1,
    'x_final' => 1.0
];

$resultado3 = aplicarMetodo($condicionesIniciales3, $parametros3, 'cuadratica');

?>