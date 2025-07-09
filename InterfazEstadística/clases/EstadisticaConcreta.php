<?php
    declare(strict_types=1);
    namespace Clases;
    require_once 'EstadisticaAbstracta.php';
    class EstadisticaConcreta extends EstadisticaAbstracta{
        public function calcularMedia(array $datos):float{
            $suma=0;
            if(empty($datos)){
                return 0.0;
            }
            $total = count($datos);
            foreach($datos as $dato){
                $suma += $dato;
            }
            return $suma/$total;
        }

        public function calcularMediana(array $datos): float{
            if(empty($datos)){
                return 0.0;
            }
            sort($datos);
            $numDatos = count($datos);
            if($numDatos%2==0){
                return (($datos[($numDatos/2)-1])+($datos[($numDatos/2)]))/2;
            }else{
                return $datos[intval($numDatos/2)];
            }
        }

        public function calcularModa(array $datos): array{
            if(empty($datos)){
                return array();
            }
            $datosString = array_map('strval', $datos);
            $frecuencias = array_count_values($datosString);
            
            if(empty($frecuencias)) {
                return array();
            }

            $maximo = max($frecuencias);
            
            $modas=array();
            foreach($frecuencias as $valor=>$frecuencia){
                if($maximo == $frecuencia){
                    $modas[]=(float)$valor;
                }
            }
            if(count($modas) == count($datos)){
                return $modas = ['error'=> "No hay moda estadística"];
            }
            return $modas;
        }

        public function mostrarInforme(array $conjunto): array{
            $informe = array();
            
            foreach($conjunto as $id => $datos){
                $orden = $datos;
                sort($orden);
                $informe[$id]=[
                    'datos' => $datos,
                    'ordenados' => $orden,
                    'media' => $this->calcularMedia($datos),
                    'moda' => $this->calcularModa($datos),
                    'mediana' => $this->calcularMediana($datos),
                    'cantidad' => count($datos)
                ];
            }
            return $informe;
        }

        public static function parseDatos(string $datosCadena):array{
            $datos = array();
            $numeros = explode(",", trim($datosCadena));

            foreach($numeros as $numero){
                $numerito = trim($numero);
                if(is_numeric($numerito)){
                    $datos[] = (float)$numerito;
                }
            }
            return $datos;
        }
    }