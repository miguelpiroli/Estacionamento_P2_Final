<?php

namespace App\Domain;

use App\Domain\Interfaces\interfaceCalcTarifa;

class tarifaCaminhao implements interfaceCalcTarifa
{
    private const VALOR_POR_HORA = 10.0;

    public function calcularTarifa(int $tempo): float
    {
        return $tempo * self::VALOR_POR_HORA;
    }
}