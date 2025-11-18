<?php

namespace App\Domain;

use App\Domain\Interfaces\interfaceCalcTarifa;

class tarifaMotocicleta implements interfaceCalcTarifa
{
    private const VALOR_POR_HORA = 3.0;

    public function calcularTarifa(int $tempo): float
    {
        return $tempo * self::VALOR_POR_HORA;
    }
}