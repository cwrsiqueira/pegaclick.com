<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();

// Verifica se o site pertence ao usuário
$usuarioId = $_SESSION['usuario_id'];
$siteId = $_GET['site_id'] ?? null;

if (!$siteId) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM sites WHERE id = ? AND usuario_id = ?");
$stmt->execute([$siteId, $usuarioId]);
$site = $stmt->fetch();

if (!$site) {
    header('Location: index.php');
    exit;
}

// Filtro de datas
$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01'); // primeiro dia do mês atual
$dataFim = $_GET['data_fim'] ?? date('Y-m-d'); // hoje

// Buscar elementos monitorados
$stmt = $pdo->prepare("SELECT * FROM elementos_monitorados WHERE site_id = ?");
$stmt->execute([$siteId]);
$elementos = $stmt->fetchAll();

// Buscar eventos agrupados por data e elemento
$stmt = $pdo->prepare("
    SELECT 
        DATE(created_at) as data_evento,
        elemento_id,
        SUM(quantidade) as total
    FROM eventos
    WHERE site_id = ?
      AND created_at BETWEEN ? AND ?
    GROUP BY DATE(created_at), elemento_id
    ORDER BY DATE(created_at)
");
$stmt->execute([$siteId, $dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);
$eventos = $stmt->fetchAll();

// Organizar eventos por elemento_id
$datas = [];
$dadosPorElemento = [];

// Mapear nome dos elementos
$nomesElementos = [];
foreach ($elementos as $el) {
    $nomesElementos[$el['id']] = $el['nome_elemento'];
}

// Organizar eventos
foreach ($eventos as $evento) {
    $dataFormatada = date('d/m/Y', strtotime($evento['data_evento']));
    if (!in_array($dataFormatada, $datas)) {
        $datas[] = $dataFormatada;
    }

    $elementoId = $evento['elemento_id'];
    if (!isset($dadosPorElemento[$elementoId])) {
        $dadosPorElemento[$elementoId] = [];
    }
    $dadosPorElemento[$elementoId][$dataFormatada] = $evento['total'];
}

// Garantir que todas as datas existam para todos os elementos
foreach ($dadosPorElemento as $id => &$dados) {
    foreach ($datas as $data) {
        if (!isset($dados[$data])) {
            $dados[$data] = 0;
        }
    }
    ksort($dados); // ordenar por data
}
unset($dados);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Detalhes do Site - Pegaclick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light p-4">
    <div class="container">

        <a href="index.php" class="btn btn-secondary mb-4">← Voltar para Painel</a>

        <div class="card p-4 mb-4">
            <h1 class="h4">Detalhes do Site</h1>
            <p><strong>Nome:</strong> <?= htmlspecialchars($site['nome_site']) ?></p>
            <p><strong>URL Base:</strong> <?= htmlspecialchars($site['url_base']) ?></p>
            <p><strong>Token:</strong> <code><?= htmlspecialchars($site['token_acesso']) ?></code></p>
        </div>

        <div class="card p-4 mb-4">
            <h2 class="h5">Filtro de Período</h2>
            <form method="GET" class="row g-2">
                <input type="hidden" name="site_id" value="<?= htmlspecialchars($siteId) ?>">
                <div class="col-md-4">
                    <label for="data_inicio" class="form-label">Data Início:</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>">
                </div>
                <div class="col-md-4">
                    <label for="data_fim" class="form-label">Data Fim:</label>
                    <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card p-4 mb-4">
            <h2 class="h5 mb-3">Eventos Registrados</h2>
            <canvas id="graficoEventos"></canvas>
        </div>

        <div class="card p-4 mb-4">
            <h2 class="h5 mb-3">Elementos Monitorados</h2>
            <?php if (count($elementos) > 0): ?>
                <ul class="list-group">
                    <?php
                    $cores = [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#fd7e14',
                        '#20c997',
                        '#6610f2',
                        '#0dcaf0',
                        '#6c757d'
                    ];
                    $corIndex = 0;
                    ?>
                    <?php foreach ($elementos as $el): ?>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-circle me-2" style="color: <?= $cores[$corIndex % count($cores)] ?>;"></i>
                            <div>
                                <strong><?= htmlspecialchars($el['nome_elemento']) ?></strong><br>
                                <small><?= htmlspecialchars($el['descricao']) ?></small>
                            </div>
                        </li>
                        <?php $corIndex++; ?>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Nenhum elemento monitorado cadastrado.</p>
            <?php endif; ?>
        </div>

    </div>

    <script>
        const ctx = document.getElementById('graficoEventos').getContext('2d');
        const graficoEventos = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($datas) ?>,
                datasets: [
                    <?php
                    $corIndex = 0;
                    foreach ($dadosPorElemento as $id => $dados):
                    ?> {
                            label: '<?= htmlspecialchars($nomesElementos[$id] ?? 'Elemento') ?>',
                            data: <?= json_encode(array_values($dados)) ?>,
                            fill: false,
                            borderColor: '<?= $cores[$corIndex % count($cores)] ?>',
                            backgroundColor: '<?= $cores[$corIndex % count($cores)] ?>',
                            tension: 0.3
                        },
                    <?php $corIndex++;
                    endforeach; ?>
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>