<?php
declare(strict_types=1);

// Clase abstracta para matrices
abstract class MatrizAbstracta {
    protected array $datos;
    protected int $filas;
    protected int $columnas;

    public function __construct(array $datos) {
        $this->datos = $datos;
        $this->filas = count($datos);
        $this->columnas = count(reset($datos));
    }

    public function getDatos(): array {
        return $this->datos;
    }

    public function getFilas(): int {
        return $this->filas;
    }

    public function getColumnas(): int {
        return $this->columnas;
    }

    abstract public function multiplicar(Matriz $matriz): ?Matriz;
    abstract public function inversa(): ?Matriz;
}

// Clase concreta
class Matriz extends MatrizAbstracta {
    private string $dimension;
    private bool $esCuadrada;

    public function __construct(array $datos) {
        parent::__construct($datos);
        $this->dimension = $this->filas . "x" . $this->columnas;
        $this->esCuadrada = ($this->filas === $this->columnas);
    }

    public function getDimension(): string {
        return $this->dimension;
    }

    public function esCuadrada(): bool {
        return $this->esCuadrada;
    }

    public function multiplicar(Matriz $otra): ?Matriz {
        // Verificar compatibilidad para multiplicación
        if ($this->columnas !== $otra->getFilas()) {
            echo "Error: Las matrices no son compatibles para multiplicación: {$this->dimension} x {$otra->getDimension()}\n";
            return null;
        }

        $m1 = $this->datos;
        $m2 = $otra->getDatos();
        
        // Obtener las claves de filas y columnas manualmente
        $filasM1 = [];
        $columnasM1 = [];
        $filasM2 = [];
        $columnasM2 = [];
        
        foreach ($m1 as $clave => $fila) {
            $filasM1[] = $clave;
            if (empty($columnasM1)) {
                foreach ($fila as $claveCol => $valor) {
                    $columnasM1[] = $claveCol;
                }
            }
        }
        
        foreach ($m2 as $clave => $fila) {
            $filasM2[] = $clave;
            if (empty($columnasM2)) {
                foreach ($fila as $claveCol => $valor) {
                    $columnasM2[] = $claveCol;
                }
            }
        }
        
        $result = [];

        // Para cada fila de la primera matriz
        foreach ($filasM1 as $i => $claveFilaM1) {
            $result[$claveFilaM1] = [];
            // Para cada columna de la segunda matriz
            foreach ($columnasM2 as $j => $claveColM2) {
                $sum = 0.0;
                
                // Producto punto entre fila i de M1 y columna j de M2
                foreach ($columnasM1 as $k => $claveColM1) {
                    $claveFilaM2 = $filasM2[$k]; // Fila correspondiente en M2
                    $sum += $m1[$claveFilaM1][$claveColM1] * $m2[$claveFilaM2][$claveColM2];
                }
                
                $result[$claveFilaM1][$claveColM2] = $sum;
            }
        }
        return new Matriz($result);
    }

    public function inversa(): ?Matriz {
        // Verificar que sea matriz cuadrada
        if (!$this->esCuadrada) {
            echo "Error: Solo las matrices cuadradas tienen inversa. Dimensión actual: {$this->dimension}\n";
            return null;
        }

        $matriz = $this->datos;
        
        // Obtener claves de filas y columnas manualmente
        $filas = [];
        $columnas = [];
        
        foreach ($matriz as $clave => $fila) {
            $filas[] = $clave;
            if (empty($columnas)) {
                foreach ($fila as $claveCol => $valor) {
                    $columnas[] = $claveCol;
                }
            }
        }
        
        // Crear matriz identidad con claves asociativas
        $identidad = [];
        foreach ($filas as $indexI => $claveI) {
            $identidad[$claveI] = [];
            foreach ($columnas as $indexJ => $claveJ) {
                $identidad[$claveI][$claveJ] = ($indexI === $indexJ) ? 1.0 : 0.0;
            }
        }

        // Aplicar eliminación de Gauss-Jordan con pivoting
        for ($i = 0; $i < count($filas); $i++) {
            $claveI = $filas[$i];
            $claveColumnaDiagonal = $columnas[$i];
            
            // Buscar pivote no cero (pivoting parcial)
            if (abs($matriz[$claveI][$claveColumnaDiagonal]) < 1e-10) {
                $filaIntercambio = null;
                
                // Buscar una fila con elemento no cero en la columna actual
                for ($k = $i + 1; $k < count($filas); $k++) {
                    $claveK = $filas[$k];
                    if (abs($matriz[$claveK][$claveColumnaDiagonal]) > 1e-10) {
                        $filaIntercambio = $claveK;
                        break;
                    }
                }
                
                if ($filaIntercambio === null) {
                    echo "Error: La matriz no es invertible (determinante = 0).\n";
                    return null;
                }
                
                // Intercambiar filas en ambas matrices
                foreach ($columnas as $claveJ) {
                    $temp = $matriz[$claveI][$claveJ];
                    $matriz[$claveI][$claveJ] = $matriz[$filaIntercambio][$claveJ];
                    $matriz[$filaIntercambio][$claveJ] = $temp;
                    
                    $temp = $identidad[$claveI][$claveJ];
                    $identidad[$claveI][$claveJ] = $identidad[$filaIntercambio][$claveJ];
                    $identidad[$filaIntercambio][$claveJ] = $temp;
                }
            }
            
            $factor = $matriz[$claveI][$claveColumnaDiagonal];
            
            if (abs($factor) < 1e-10) {
                echo "Error: La matriz no es invertible (determinante = 0).\n";
                return null;
            }

            // Normalizar la fila pivote
            foreach ($columnas as $claveJ) {
                $matriz[$claveI][$claveJ] /= $factor;
                $identidad[$claveI][$claveJ] /= $factor;
            }

            // Eliminar elementos en otras filas
            foreach ($filas as $indexK => $claveK) {
                if ($indexK != $i) {
                    $factor = $matriz[$claveK][$claveColumnaDiagonal];
                    foreach ($columnas as $claveJ) {
                        $matriz[$claveK][$claveJ] -= $factor * $matriz[$claveI][$claveJ];
                        $identidad[$claveK][$claveJ] -= $factor * $identidad[$claveI][$claveJ];
                    }
                }
            }
        }

        return new Matriz($identidad);
    }
}

// Función recursiva para calcular el determinante
function determinante(array $matriz): float {
    // Obtener claves manualmente
    $filas = [];
    $columnas = [];
    
    foreach ($matriz as $clave => $fila) {
        $filas[] = $clave;
        if (empty($columnas)) {
            foreach ($fila as $claveCol => $valor) {
                $columnas[] = $claveCol;
            }
        }
    }
    
    $n = count($filas);

    if ($n == 1) {
        $primerFila = $filas[0];
        $primerCol = $columnas[0];
        return (float)$matriz[$primerFila][$primerCol];
    }

    if ($n == 2) {
        $fila1 = $filas[0];
        $fila2 = $filas[1];
        $col1 = $columnas[0];
        $col2 = $columnas[1];
        return (float)($matriz[$fila1][$col1] * $matriz[$fila2][$col2] - 
               $matriz[$fila1][$col2] * $matriz[$fila2][$col1]);
    }

    $det = 0.0;
    $primeraFila = $filas[0];
    
    foreach ($columnas as $index => $j) {
        $val = (float)$matriz[$primeraFila][$j];
        
        // Crear submatriz excluyendo primera fila y columna j
        $submatriz = [];
        $nuevaFilaIndex = 0;
        
        // Copiar filas excepto la primera
        for ($f = 1; $f < count($filas); $f++) {
            $filaOriginal = $filas[$f];
            $nuevaClaveFila = "sf_$nuevaFilaIndex";
            $submatriz[$nuevaClaveFila] = [];
            
            $nuevaColIndex = 0;
            foreach ($columnas as $colIndex => $claveCol) {
                if ($colIndex != $index) { // Excluir columna j
                    $nuevaClaveCol = "sc_$nuevaColIndex";
                    $submatriz[$nuevaClaveFila][$nuevaClaveCol] = $matriz[$filaOriginal][$claveCol];
                    $nuevaColIndex++;
                }
            }
            $nuevaFilaIndex++;
        }
        
        $signo = ($index % 2 == 0) ? 1 : -1;
        $det += $signo * $val * determinante($submatriz);
    }

    return $det;
}

// Leer matriz desde consola usando arrays asociativos
function leerMatriz(string $nombre): array {
    echo "Ingrese la matriz '$nombre':\n";
    $n = (int) readline("Número de filas/columnas (matriz cuadrada): ");
    $matriz = [];

    for ($i = 0; $i < $n; $i++) {
        echo "Fila #$i:\n";
        $claveI = "fila_$i";
        $matriz[$claveI] = [];
        
        for ($j = 0; $j < $n; $j++) {
            $claveJ = "col_$j";
            $matriz[$claveI][$claveJ] = (float) readline("  Elemento [$i][$j]: ");
        }
    }

    return $matriz;
}

// Mostrar matriz con 3 decimales
function mostrarMatriz(array $matriz): void {
    foreach ($matriz as $claveFila => $fila) {
        $valores = [];
        foreach ($fila as $claveCol => $valor) {
            $valores[] = number_format($valor, 3);
        }
        echo implode("\t", $valores) . "\n";
    }
}

// Función principal
function ejecutarOperacionesMatriz(): void {
    echo "=== Operaciones con Matrices (Arrays Asociativos) ===\n";

    $datos1 = leerMatriz("A");
    $datos2 = leerMatriz("B");

    $matrizA = new Matriz($datos1);
    $matrizB = new Matriz($datos2);

    echo "\nInformación de matrices:\n";
    echo "Matriz A - Dimensión: {$matrizA->getDimension()}, Es cuadrada: " . ($matrizA->esCuadrada() ? "Sí" : "No") . "\n";
    echo "Matriz B - Dimensión: {$matrizB->getDimension()}, Es cuadrada: " . ($matrizB->esCuadrada() ? "Sí" : "No") . "\n\n";

    $matrices = [
        'Matriz_A' => $matrizA,
        'Matriz_B' => $matrizB
    ];

    // Mostrar cada matriz y su inversa
    foreach ($matrices as $nombre => $matriz) {
        echo "=== $nombre (Dimensión: {$matriz->getDimension()}) ===\n";
        mostrarMatriz($matriz->getDatos());
        
        if ($matriz->esCuadrada()) {
            echo "Determinante de $nombre: " . number_format(determinante($matriz->getDatos()), 6) . "\n";

            echo "Inversa de $nombre:\n";
            $inversa = $matriz->inversa();
            if ($inversa !== null) {
                mostrarMatriz($inversa->getDatos());
            }
        } else {
            echo "La matriz $nombre no es cuadrada, no se puede calcular determinante ni inversa.\n";
        }
        echo "\n";
    }

    // Al final mostrar la multiplicación A*B
    echo "=== MULTIPLICACIÓN A * B ===\n";
    $producto = $matrizA->multiplicar($matrizB);
    if ($producto !== null) {
        mostrarMatriz($producto->getDatos());
        echo "Resultado - Dimensión: {$producto->getDimension()}\n";
    }
}

// Ejecutar
ejecutarOperacionesMatriz();

?>