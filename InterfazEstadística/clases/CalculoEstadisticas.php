<?php
    declare(strict_types=1);
    namespace Clases;
    interface EstadisticaInterface{
        public function calcularMedia(array $datos): float;
        public function calcularMediana(array $datos): float;
        public function calcularModa(array $datos): array;
        public function mostrarInforme(array $conjunto): array;
    }