<?php
    declare(strict_types=1);
    namespace Clases;
    interface CalculoPolinomios{
        public function derivada():array;
        public function evaluar(float $x):float;
        public function mostrarPolinomio():string;
    }