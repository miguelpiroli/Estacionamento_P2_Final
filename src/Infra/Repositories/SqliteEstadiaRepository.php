<?php

namespace App\Infra\Repositories;

use App\Domain\EstadiaEstacionamento;
use App\Domain\Interfaces\RegistroEstacionamentoRepositoryInterface;
use App\Domain\TipoVeiculo;
use App\Infra\SqliteConnection;
use DateTimeImmutable;
use PDO;

class SqliteEstadiaRepository implements RegistroEstacionamentoRepositoryInterface
{
    private PDO $pdo;

    public function __construct(SqliteConnection $connection)
    {
        $this->pdo = $connection->getConnection();
    }

    public function save(EstadiaEstacionamento $registro): void
    {
        if ($registro->getId() !== null) {
            $this->update($registro);
        } else {
            $this->insert($registro);
        }
    }

    private function insert(EstadiaEstacionamento $registro): void
    {
        $sql = "INSERT INTO estadias (placa, tipo_veiculo, data_entrada, data_saida, valor_total)
                VALUES (:placa, :tipo, :entrada, :saida, :valor)";
        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            ':placa' => $registro->getPlaca(),
            ':tipo' => $registro->getTipoVeiculo()->value,
            ':entrada' => $registro->getDataEntrada()->format('Y-m-d H:i:s'),
            ':saida' => $registro->getDataSaida()?->format('Y-m-d H:i:s'),
            ':valor' => $registro->getValorTotal()
        ]);

        $this->setPrivateProperty($registro, 'id', (int)$this->pdo->lastInsertId());
    }

    private function update(EstadiaEstacionamento $registro): void
    {
        $sql = "UPDATE estadias SET
                    placa = :placa,
                    tipo_veiculo = :tipo,
                    data_entrada = :entrada,
                    data_saida = :saida,
                    valor_total = :valor
                WHERE id = :id";
        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            ':id' => $registro->getId(),
            ':placa' => $registro->getPlaca(),
            ':tipo' => $registro->getTipoVeiculo()->value,
            ':entrada' => $registro->getDataEntrada()->format('Y-m-d H:i:s'),
            ':saida' => $registro->getDataSaida()?->format('Y-m-d H:i:s'),
            ':valor' => $registro->getValorTotal()
        ]);
    }

    public function findById(int $id): ?EstadiaEstacionamento
    {
        $consulta = $this->pdo->prepare("SELECT * FROM estadias WHERE id = :id");
        $consulta->execute([':id' => $id]);
        $registroArray = $consulta->fetch();
        return $registroArray ? $this->mapRowToEntity($registroArray) : null;
    }

    public function findByPlacaAtiva(string $placa): ?EstadiaEstacionamento
    {
        $consulta = $this->pdo->prepare("SELECT * FROM estadias WHERE placa = :placa AND data_saida IS NULL LIMIT 1");
        $consulta->execute([':placa' => $placa]);
        $registroArray = $consulta->fetch();
        return $registroArray ? $this->mapRowToEntity($registroArray) : null;
    }

    public function findAll(): array
    {
        $consulta = $this->pdo->query("SELECT * FROM estadias ORDER BY data_entrada DESC");
        $registrosArray = $consulta->fetchAll();
        return array_map(fn($linha) => $this->mapRowToEntity($linha), $registrosArray);
    }

    private function mapRowToEntity(array $registroArray): EstadiaEstacionamento
    {
        return new EstadiaEstacionamento(
            placa: $registroArray['placa'],
            tipoVeiculo: TipoVeiculo::from($registroArray['tipo_veiculo']),
            dataEntrada: new DateTimeImmutable($registroArray['data_entrada']),
            dataSaida: $registroArray['data_saida'] ? new DateTimeImmutable($registroArray['data_saida']) : null,
            valorTotal: $registroArray['valor_total'] !== null ? (float)$registroArray['valor_total'] : null,
            id: (int)$registroArray['id']
        );
    }

    private function setPrivateProperty(object $obj, string $prop, mixed $value): void
    {
        $r = new \ReflectionClass($obj);
        $p = $r->getProperty($prop);
        $p->setAccessible(true);
        $p->setValue($obj, $value);
    }

    private function mapToEntity(array $row): EstadiaEstacionamento
    {
        $estadia = new EstadiaEstacionamento(
            placa: $row['placa'],
            tipoVeiculo: TipoVeiculo::from($row['tipo_veiculo']),
            dataEntrada: new DateTimeImmutable($row['data_entrada'])
        );

        if ($row['id']) {
            $reflection = new \ReflectionClass($estadia);
            $idProperty = $reflection->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($estadia, (int)$row['id']);
        }

        if ($row['data_saida']) {
            $estadia->registrarSaida(new DateTimeImmutable($row['data_saida']));
        }

        if ($row['valor_total']) {
            $estadia->registrarValorTotal((float)$row['valor_total']);
        }

        return $estadia;
    }
}
