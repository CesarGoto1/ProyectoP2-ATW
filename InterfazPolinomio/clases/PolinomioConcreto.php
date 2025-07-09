<?php

declare(strict_types=1);
namespace Clases;
require_once 'PolinomioAbstracto.php';

class PolinomioConcreto extends PolinomioAbstracto {
    
    public function evaluar(float $x): float {
        $resultado = 0.0;
        foreach ($this->terminos as $grado => $coeficiente) {
            $resultado += $coeficiente * pow($x, $grado);
        }
        return $resultado;
    }

    public function derivada(): array {
        $derivadaTerminos = [];
        foreach ($this->terminos as $grado => $coeficiente) {
            if ($grado != 0) {
                $derivadaTerminos[$grado - 1] = $coeficiente * $grado;
            }
        }
        return $derivadaTerminos;
    }

    public function obtenerDerivada(): PolinomioConcreto {
        return new PolinomioConcreto($this->derivada());
    }
    public function mostrarPolinomio():string{
        $resultado = "";
        $primero = true;

        krsort($this->terminos);
        
        foreach ($this->terminos as $grado => $coeficiente) {
            if ($coeficiente == 0) continue;
            
            if (!$primero && $coeficiente > 0) {
                $resultado .= " + ";
            } elseif ($coeficiente < 0) {
                $resultado .= $primero ? "-" : " - ";
                $coeficiente = abs($coeficiente);
            }
            
            if ($grado == 0) {
                $resultado .= $coeficiente;
            } elseif ($grado == 1) {
                $resultado .= ($coeficiente == 1 ? "" : $coeficiente) . "x";
            } elseif ($grado == -1) {
                $resultado .= ($coeficiente == 1 ? "" : $coeficiente) . "x<sup>-1</sup>";
            } elseif ($grado > 1) {
                $resultado .= ($coeficiente == 1 ? "" : $coeficiente) . "x<sup>" . $grado . "</sup>";
            } else { // grado < -1
                $resultado .= ($coeficiente == 1 ? "" : $coeficiente) . "x<sup>" . $grado . "</sup>";
            }
            
            $primero = false;
        }
        
        return $resultado ?: "0";
    }
    public static function sumarPolinomios(array $p1, array $p2): array {
        $resultado = $p1;
        
        foreach ($p2 as $grado => $coeficiente) {
            if (isset($resultado[$grado])) {
                $resultado[$grado] += $coeficiente;
            } else {
                $resultado[$grado] = $coeficiente;
            }
        }
        
        return $resultado;
    }


    public static function parsePolinomio(string $str): array {
        $terminos = [];
        $partes = explode(",", trim($str));
        
        foreach ($partes as $parte) {
            $termino = explode(":", trim($parte));
            if (count($termino) == 2) {
                $grado = (int)trim($termino[0]);
                $coeficiente = (float)trim($termino[1]);
                $terminos[$grado] = $coeficiente;
            }
        }
        
        return $terminos;
    }
}