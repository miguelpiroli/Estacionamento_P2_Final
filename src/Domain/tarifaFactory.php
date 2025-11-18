<?php

namespace App\Domain;

use App\Domain\tipoVeiculo;
use App\Domain\tarifaCarro;
use App\Domain\tarifaMotocicleta;
use App\Domain\tarifaCaminhao;
use App\Domain\Interfaces\interfaceCalcTarifa;

class tarifaFactory {
    public static function create(tipoVeiculo $tipoVeiculo): interfaceCalcTarifa {
        
        return match ($tipoVeiculo) {
            tipoVeiculo::Carro => new tarifaCarro(),
            tipoVeiculo::Motocicleta => new tarifaMotocicleta(),
            tipoVeiculo::Caminhao => new tarifaCaminhao(),
        };
    }
}