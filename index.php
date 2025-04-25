<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();

// Buscar os sites cadastrados do usuário
$usuarioId = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT * FROM sites WHERE usuario_id = ?");
$stmt->execute([$usuarioId]);
$sites = $stmt->fetchAll();

// Site selecionado para ver estatísticas
$siteIdSelecionado = $_GET['site_id'] ?? null;
$eventos = [];
$elementosPorSite = [];

// Buscar elementos monitorados para todos os sites
if ($sites) {
    $siteIds = array_column($sites, 'id');
    $in = str_repeat('?,', count($siteIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM elementos_monitorados WHERE site_id IN ($in)");
    $stmt->execute($siteIds);
    $todosElementos = $stmt->fetchAll();

    foreach ($todosElementos as $el) {
        $elementosPorSite[$el['site_id']][] = $el;
    }
}

// Se selecionou site para ver eventos
if ($siteIdSelecionado) {
    $stmt = $pdo->prepare("
        SELECT 
            eventos.pagina, 
            eventos.evento, 
            elementos_monitorados.nome_elemento, 
            elementos_monitorados.descricao,
            SUM(eventos.quantidade) as total
        FROM eventos
        LEFT JOIN elementos_monitorados 
            ON eventos.elemento_id = elementos_monitorados.id
        WHERE eventos.site_id = ?
        GROUP BY eventos.pagina, eventos.evento, eventos.elemento_id
        ORDER BY total DESC
    ");
    $stmt->execute([$siteIdSelecionado]);
    $eventos = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel - Pegaclick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Painel Pegaclick</h1>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>

        <div class="mb-4">
            <a href="cadastrar_site.php" class="btn btn-success mb-2">+ Cadastrar Novo Site</a>
            <a href="cadastrar_elemento.php" class="btn btn-primary mb-2">+ Cadastrar Novo Elemento</a>
            <a href="doc.php" class="btn btn-secondary mb-2">📄 Documentação</a>
        </div>

        <div class="card p-4 bg-white mb-4">
            <h2 class="h4 mb-3">Seus Sites</h2>

            <?php if (count($sites) > 0): ?>
                <?php foreach ($sites as $site): ?>
                    <div class="mb-4">
                        <h5><?= htmlspecialchars($site['nome_site']) ?></h5>
                        <p class="mb-1"><strong>URL Base:</strong> <?= htmlspecialchars($site['url_base']) ?></p>
                        <p><strong>Token de Acesso:</strong> <code><?= htmlspecialchars($site['token_acesso']) ?></code></p>

                        <?php if (isset($elementosPorSite[$site['id']])): ?>
                            <h6>Elementos Monitorados:</h6>
                            <ul class="list-group mb-3">
                                <?php foreach ($elementosPorSite[$site['id']] as $el): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($el['nome_elemento']) ?></strong>
                                        <br>
                                        <small><?= htmlspecialchars($el['descricao']) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">Nenhum elemento cadastrado para este site.</p>
                        <?php endif; ?>

                        <a href="index.php?site_id=<?= $site['id'] ?>" class="btn btn-outline-primary btn-sm">Ver Estatísticas</a>
                        <hr>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Nenhum site cadastrado ainda.</p>
            <?php endif; ?>
        </div>

        <?php if ($siteIdSelecionado): ?>
            <div class="card p-4 bg-white">
                <h2 class="h4 mb-3">Estatísticas do Site</h2>

                <?php if (count($eventos) > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Página</th>
                                <th>Evento</th>
                                <th>Elemento</th>
                                <th>Descrição</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eventos as $evento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($evento['pagina']) ?></td>
                                    <td><?= htmlspecialchars($evento['evento']) ?></td>
                                    <td><?= htmlspecialchars($evento['nome_elemento'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($evento['descricao'] ?? '-') ?></td>
                                    <td class="text-center"><?= $evento['total'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Nenhum evento registrado para este site.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>