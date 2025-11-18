<?php

namespace App\Domain\registrarEstacionamento;

use DateTimeImmutable;
use Exception;

class registrarEstacionamento
{
    public function __construct(
        private string $placa,
        private TipoVeiculo $tipoVeiculo,
        private DateTimeImmutable $dataEntrada,
        private ?DateTimeImmutable $dataSaida = null,
        private ?float $valorTotal = null,
        private ?int $id = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getPlaca(): string { return $this->placa; }
    public function getTipoVeiculo(): TipoVeiculo { return $this->tipoVeiculo; }
    public function getDataEntrada(): DateTimeImmutable { return $this->dataEntrada; }
    public function getDataSaida(): ?DateTimeImmutable { return $this->dataSaida; }
    public function getValorTotal(): ?float { return $this->valorTotal; }

    public function isFinalizado(): bool
    {
        return $this->dataSaida !== null;
    }

    public function registrarSaida(DateTimeImmutable $dataSaida): void
    {
        if ($this->isFinalizado()) {
            throw new Exception("A saída já foi registrada.");
        }

        if ($dataSaida < $this->dataEntrada) {
            throw new Exception("A saída não pode ser anterior à entrada.");
        }

        $this->dataSaida = $dataSaida;
    }

    public function registrarValorTotal(float $valor): void
    {
        $this->valorTotal = $valor;
    }

    public function calcularHorasPermanencia(): int
    {
        if (!$this->isFinalizado()) {
            return 0; 
        }

        $minutosTotais = $this->calcularTotalMinutos();

        return max(1, (int) ceil($minutosTotais / 60));
    }

    private function calcularTotalMinutos(): int
    {
        $intervalo = $this->dataEntrada->diff($this->dataSaida);

        return ($intervalo->days * 24 * 60)
            + ($intervalo->h * 60)
            + $intervalo->i;
    }
}
