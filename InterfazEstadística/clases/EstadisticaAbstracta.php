<?php
    declare(strict_types=1);
    namespace Clases;

    require_once 'CalculoEstadisticas.php';

    abstract class EstadisticaAbstracta implements EstadisticaInterface{
        protected array $conjunto;

        public function __construct(array $conjunto){
            $this->conjunto = $conjunto;
        }

        public function setConjunto(array $conjunto): void{
            $this->conjunto = $conjunto;
        }
        public function getConjunto():array{
            return $this->conjunto;
        }
        public function nuevoConjunto(string $id, array $datos): void{
            $this->datos[$id] = $datos;
        }
        abstract public function calcularMedia(array $datos): float;
        abstract public function calcularMediana(array $datos): float;
        abstract public function calcularModa(array $datos): array;
        abstract public function mostrarInforme(array $conjunto): array;
        public function mostrarConjunto(string $id): string{
            if(!isset($this->conjunto[$id])){
                return "Conjunto no encontrado";
            }
            $datos = $this->conjunto[$id];
            return $id . ": [". implode(",", $datos) ."]";
        }
    }