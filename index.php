<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();

// Buscar os sites do usuário
$usuarioId = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT * FROM sites WHERE usuario_id = ?");
$stmt->execute([$usuarioId]);
$sites = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Pegaclick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Painel Pegaclick</h1>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>

        <!-- Ações -->
        <div class="mb-4">
            <a href="cadastrar_site.php" class="btn btn-success mb-2">+ Cadastrar Novo Site</a>
            <a href="cadastrar_elemento.php" class="btn btn-primary mb-2">+ Cadastrar Novo Elemento</a>
            <a href="doc.php" class="btn btn-secondary mb-2">📄 Documentação</a>
        </div>

        <!-- Lista de Sites -->
        <div class="card p-4 bg-white">
            <h2 class="h4 mb-3">Seus Sites</h2>

            <?php if (count($sites) > 0): ?>
                <div class="list-group">
                    <?php foreach ($sites as $site): ?>
                        <div class="list-group-item mb-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5><?= htmlspecialchars($site['nome_site']) ?></h5>
                                    <small><strong>URL Base:</strong> <?= htmlspecialchars($site['url_base']) ?></small><br>
                                    <small><strong>Token:</strong> <code><?= htmlspecialchars($site['token_acesso']) ?></code></small>
                                </div>
                                <div class="d-flex flex-column justify-content-center gap-2">
                                    <a href="site.php?site_id=<?= $site['id'] ?>" class="btn btn-outline-primary btn-sm">📊 Ver Estatísticas</a>
                                    <a href="editar_site.php?site_id=<?= $site['id'] ?>" class="btn btn-outline-warning btn-sm">✏️ Editar Site</a>
                                    <a href="excluir_site.php?site_id=<?= $site['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este site?');">🗑️ Excluir Site</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Nenhum site cadastrado ainda.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>