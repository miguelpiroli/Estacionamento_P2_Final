<?php

namespace App\Application\Services;

use App\Domain\Interfaces\RegistroEstacionamentoRepositoryInterface;
use App\Domain\TipoVeiculo;

class GerarRelatorioService
{
    public function __construct(
        private RegistroEstacionamentoRepositoryInterface $repository
    ) {}

    public function executar(): array
    {
        $estadias = $this->repository->findAll();

        $relatorio = [
            'total_veiculos' => 0,
            'veiculos_ativos' => 0,
            'veiculos_finalizados' => 0,
            'faturamento_total' => 0.0,
            'por_tipo' => []
        ];

        foreach (TipoVeiculo::cases() as $tipo) {
            $relatorio['por_tipo'][$tipo->value] = [
                'quantidade' => 0,
                'quantidade_ativa' => 0,
                'quantidade_finalizada' => 0,
                'faturamento' => 0.0
            ];
        }

        foreach ($estadias as $estadia) {
            $tipo = $estadia->getTipoVeiculo()->value;

            $relatorio['total_veiculos']++;
            $relatorio['por_tipo'][$tipo]['quantidade']++;

            if ($estadia->isFinalizado()) {
                $relatorio['veiculos_finalizados']++;
                $relatorio['por_tipo'][$tipo]['quantidade_finalizada']++;

                $valor = $estadia->getValorTotal() ?? 0.0;
                $relatorio['faturamento_total'] += $valor;
                $relatorio['por_tipo'][$tipo]['faturamento'] += $valor;
            } else {
                $relatorio['veiculos_ativos']++;
                $relatorio['por_tipo'][$tipo]['quantidade_ativa']++;
            }
        }

        return $relatorio;
    }
}
