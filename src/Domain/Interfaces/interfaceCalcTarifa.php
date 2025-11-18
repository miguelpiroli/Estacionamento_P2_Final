<?php

namespace App\Domain\Interfaces;

interface interfaceCalcTarifa
{
    public function calcularTarifa(int $tempo): float;
}
