<?php
    declare(strict_types=1);
    namespace Clases;
    require_once 'Polinomio.php';
    abstract class PolinomioAbstracto implements CalculoPolinomios{
        protected array $terminos;

        public function __construct(array $terminos){
            $this->terminos = $terminos;
        }
        public function setTerminos(array $terminos):void{
            $this->terminos = $terminos;
        }
        public function getTerminos():array{
            return $this->terminos;
        }
        abstract public function derivada():array;
        abstract public function evaluar(float $x):float;
        abstract public function mostrarPolinomio(): string;
    }