<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Application\Services\RegistrarEntradaService;
use App\Application\Services\RegistrarSaidaService;
use App\Application\Services\GerarRelatorioService;
use App\Infra\SqliteConnection;
use App\Infra\Repositories\SqliteEstadiaRepository;

$connection = SqliteConnection::getInstance();
$repository = new SqliteEstadiaRepository($connection);

$mensagem = '';
$erro = '';
$acao = $_POST['acao'] ?? $_GET['acao'] ?? 'home';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($acao) {
            case 'entrada':
                $placa = $_POST['placa'] ?? '';
                $tipo = $_POST['tipo_veiculo'] ?? '';

                $service = new RegistrarEntradaService($repository);
                $estadia = $service->executar($placa, $tipo);

                $mensagem = "Entrada registrada com sucesso! Placa: {$estadia->getPlaca()}";
                break;

            case 'saida':
                $placa = $_POST['placa'] ?? '';

                $service = new RegistrarSaidaService($repository);
                $estadia = $service->executar($placa);

                $mensagem = sprintf(
                    "Saída registrada! Placa: %s | Tempo: %dh | Valor: R$ %.2f",
                    $estadia->getPlaca(),
                    $estadia->calcularHorasPermanencia(),
                    $estadia->getValorTotal()
                );
                break;
        }
    }
} catch (Exception $e) {
    $erro = $e->getMessage();
}

$relatorioService = new GerarRelatorioService($repository);
$relatorio = $relatorioService->executar();
$estadiasAtivas = array_filter($repository->findAll(), fn($e) => !$e->isFinalizado());

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estacionamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 text-blue-600">🚗 Sistema de Estacionamento</h1>

        <?php if ($mensagem): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: '<?= htmlspecialchars($mensagem) ?>',
                    confirmButtonColor: '#3B82F6'
                });
            </script>
        <?php endif; ?>

        <?php if ($erro): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: '<?= htmlspecialchars($erro) ?>',
                    confirmButtonColor: '#EF4444'
                });
            </script>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-6 mb-8">


            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-green-600">➕ Registrar Entrada</h2>
                <form method="POST" action="index.php" class="space-y-4">
                    <input type="hidden" name="acao" value="entrada">

                    <div>
                        <label class="block text-sm font-medium mb-2">Placa do Veículo</label>
                        <input type="text" name="placa" required
                            pattern="[A-Za-z]{3}[0-9][A-Za-z0-9][0-9]{2}"
                            placeholder="ABC1D34"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 uppercase">
                        <p class="text-xs text-gray-500 mt-1">Formato: ABC1234 ou ABC1D23</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Tipo de Veículo</label>
                        <select name="tipo_veiculo" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Selecione...</option>
                            <option value="carro">🚗 Carro (R$ 5/h)</option>
                            <option value="motocicleta">🏍️ Moto (R$ 3/h)</option>
                            <option value="caminhao">🚚 Caminhão (R$ 10/h)</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                        Registrar Entrada
                    </button>
                </form>
            </div>


            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-red-600">➖ Registrar Saída</h2>
                <form method="POST" action="index.php" class="space-y-4">
                    <input type="hidden" name="acao" value="saida">

                    <div>
                        <label class="block text-sm font-medium mb-2">Placa do Veículo</label>
                        <input type="text" name="placa" required
                            placeholder="ABC1D34"
                            list="placas-ativas"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 uppercase">
                        <datalist id="placas-ativas">
                            <?php foreach ($estadiasAtivas as $estadia): ?>
                                <option value="<?= htmlspecialchars($estadia->getPlaca()) ?>">
                                <?php endforeach; ?>
                        </datalist>
                    </div>

                    <button type="submit"
                        class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                        Registrar Saída
                    </button>
                </form>

                <?php if (!empty($estadiasAtivas)): ?>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium mb-2">Veículos Ativos:</p>
                        <div class="text-xs space-y-1">
                            <?php foreach (array_slice($estadiasAtivas, 0, 5) as $estadia): ?>
                                <div class="flex justify-between">
                                    <span class="font-mono"><?= htmlspecialchars($estadia->getPlaca()) ?></span>
                                    <span class="text-gray-600"><?= ucfirst($estadia->getTipoVeiculo()->value) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4 text-purple-600">📊 Relatório Geral</h2>

            <div class="grid md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Total de Veículos</p>
                    <p class="text-3xl font-bold text-blue-600"><?= $relatorio['total_veiculos'] ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Veículos Ativos</p>
                    <p class="text-3xl font-bold text-green-600"><?= $relatorio['veiculos_ativos'] ?></p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Finalizados</p>
                    <p class="text-3xl font-bold text-gray-600"><?= $relatorio['veiculos_finalizados'] ?></p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Faturamento Total</p>
                    <p class="text-3xl font-bold text-purple-600">R$ <?= number_format($relatorio['faturamento_total'], 2, ',', '.') ?></p>
                </div>
            </div>

            <h3 class="text-xl font-bold mb-3">Detalhamento por Tipo</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-center">Total</th>
                            <th class="px-4 py-2 text-center">Ativos</th>
                            <th class="px-4 py-2 text-center">Finalizados</th>
                            <th class="px-4 py-2 text-right">Faturamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorio['por_tipo'] as $tipo => $dados): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <?php
                                    $icones = ['carro' => '🚗', 'motocicleta' => '🏍️', 'caminhao' => '🚚'];
                                    echo $icones[$tipo] . ' ' . ucfirst($tipo);
                                    ?>
                                </td>
                                <td class="px-4 py-2 text-center"><?= $dados['quantidade'] ?></td>
                                <td class="px-4 py-2 text-center text-green-600"><?= $dados['quantidade_ativa'] ?></td>
                                <td class="px-4 py-2 text-center text-gray-600"><?= $dados['quantidade_finalizada'] ?></td>
                                <td class="px-4 py-2 text-right font-semibold">
                                    R$ <?= number_format($dados['faturamento'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>