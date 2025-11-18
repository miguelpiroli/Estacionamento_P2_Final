<?php

namespace App\Domain\Interfaces;

use App\Domain\EstadiaEstacionamento;

interface RegistroEstacionamentoRepositoryInterface
{
    public function save(EstadiaEstacionamento $registro): void;

    public function findById(int $id): ?EstadiaEstacionamento;

    /**
     * @return EstadiaEstacionamento[]
     */
    public function findAll(): array;

    public function findByPlacaAtiva(string $placa): ?EstadiaEstacionamento;
}
