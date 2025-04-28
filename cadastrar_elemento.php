<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();

// Buscar os sites cadastrados para listar no select
$usuarioId = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT * FROM sites WHERE usuario_id = ?");
$stmt->execute([$usuarioId]);
$sites = $stmt->fetchAll();

$siteId = $_GET['site_id'] ?? null;

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteId = $siteId ?? $_POST['site_id'];
    $nomeElemento = trim($_POST['nome_elemento']);
    $descricao = trim($_POST['descricao']);

    // Inserir elemento monitorado
    $stmt = $pdo->prepare("INSERT INTO elementos_monitorados (site_id, nome_elemento, descricao) VALUES (?, ?, ?)");
    $stmt->execute([$siteId, $nomeElemento, $descricao]);

    header('Location: site.php?site_id=' . $siteId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Elemento - Pegaclick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h1 class="mb-4">Cadastrar Elemento Monitorado</h1>

        <form method="POST" class="card p-4 bg-white">
            <div class="mb-3">
                <label>Site:</label>
                <select class="form-select" disabled>
                    <option value="">Selecione um site</option>
                    <?php foreach ($sites as $site): ?>
                        <option <?= ($siteId == $site['id']) ? 'selected' : '' ?> value="<?= $site['id'] ?>"><?= htmlspecialchars($site['nome_site']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="site_id" value="<?= $siteId ?>">
            </div>

            <div class="mb-3">
                <label>Nome do Elemento (data-monitorar-pegaclick):</label>
                <input type="text" name="nome_elemento" class="form-control" required>
                <small class="text-muted">Exemplo: btn-pdf, link-contato, etc.</small>
            </div>

            <div class="mb-3">
                <label>Descrição:</label>
                <textarea name="descricao" class="form-control" rows="2" required></textarea>
                <small class="text-muted">Exemplo: Botão de download do PDF de resultados.</small>
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar Elemento</button>
            <a href="site.php?site_id=<?= $siteId ?>" class="btn btn-link">Voltar</a>
        </form>
    </div>
</body>

</html>