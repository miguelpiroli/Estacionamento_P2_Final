<?php

namespace App\Domain;

use App\Domain\Interfaces\interfaceCalcTarifa;

class tarifaCarro implements interfaceCalcTarifa
{
    private const VALOR_POR_HORA = 5.0;

    public function calcularTarifa(int $tempo): float
    {
        return $tempo * self::VALOR_POR_HORA;
    }
}   