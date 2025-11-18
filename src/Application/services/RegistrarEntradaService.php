<?php

namespace App\Application\Services;

use App\Domain\EstadiaEstacionamento;
use App\Domain\tipoVeiculo;
use App\Domain\Interfaces\RegistroEstacionamentoRepositoryInterface;
use DateTimeImmutable;
use Exception;

class RegistrarEntradaService
{
    public function __construct(
        private RegistroEstacionamentoRepositoryInterface $repository
    ) {}

    public function executar(string $placa, string $tipoVeiculo): EstadiaEstacionamento
    {
        $placa = strtoupper(trim($placa));

        if (empty($placa)) {
            throw new Exception("A placa do veículo não pode estar vazia.");
        }

        if (!$this->validarPlaca($placa)) {
            throw new Exception("Formato de placa inválido.");
        }

        $tipo = TipoVeiculo::tryFrom($tipoVeiculo);
        if ($tipo === null) {
            throw new Exception("Tipo de veículo inválido.");
        }

        $estadiaAtiva = $this->repository->findByPlacaAtiva($placa);
        if ($estadiaAtiva !== null) {
            throw new Exception("Este veículo já possui uma entrada ativa no estacionamento.");
        }

        $estadia = new EstadiaEstacionamento(
            placa: $placa,
            tipoVeiculo: $tipo,
            dataEntrada: new DateTimeImmutable()
        );

        $this->repository->save($estadia);

        return $estadia;
    }

    private function validarPlaca(string $placa): bool
    {
        // Formato brasileiro: ABC1234 ou ABC1D23
        return preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', $placa) === 1;
    }
}
