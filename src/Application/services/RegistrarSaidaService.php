<?php

namespace App\Application\Services;

use App\Domain\EstadiaEstacionamento;
use App\Domain\TarifaFactory;
use App\Domain\Interfaces\RegistroEstacionamentoRepositoryInterface;
use DateTimeImmutable;
use Exception;

class RegistrarSaidaService
{
    public function __construct(
        private RegistroEstacionamentoRepositoryInterface $repository
    ) {}

    public function executar(string $placa): EstadiaEstacionamento
    {
        $placa = strtoupper(trim($placa));

        if (empty($placa)) {
            throw new Exception("A placa do veículo não pode estar vazia.");
        }

        $estadia = $this->repository->findByPlacaAtiva($placa);

        if ($estadia === null) {
            throw new Exception("Nenhuma entrada ativa encontrada para esta placa.");
        }

        $dataSaida = new DateTimeImmutable();
        $estadia->registrarSaida($dataSaida);

        $horasPermanencia = $estadia->calcularHorasPermanencia();

        $calculadoraTarifa = TarifaFactory::create($estadia->getTipoVeiculo());
        $valorTotal = $calculadoraTarifa->calcularTarifa($horasPermanencia);

        $estadia->registrarValorTotal($valorTotal);

        $this->repository->save($estadia);

        return $estadia;
    }
}
