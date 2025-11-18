<?php

namespace App\Domain;

enum TipoVeiculo: string
{
    case Carro = 'carro';
    case Motocicleta = 'motocicleta';
    case Caminhao = 'caminhao';
}