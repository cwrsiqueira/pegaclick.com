<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeSite = trim($_POST['nome_site']);
    $urlBase = trim($_POST['url_base']);

    if ($nomeSite && $urlBase) {
        $stmt = $pdo->prepare("UPDATE sites SET nome_site = ?, url_base = ? WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$nomeSite, $urlBase, $siteId, $usuarioId]);
        header('Location: index.php');
        exit;
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Site - Pegaclick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">

        <a href="index.php" class="btn btn-secondary mb-4">← Voltar para Painel</a>

        <div class="card p-4">
            <h1 class="h4 mb-4">Editar Site</h1>

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="nome_site" class="form-label">Nome do Site:</label>
                    <input type="text" name="nome_site" id="nome_site" class="form-control" value="<?= htmlspecialchars($site['nome_site']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="url_base" class="form-label">URL Base:</label>
                    <input type="text" name="url_base" id="url_base" class="form-control" value="<?= htmlspecialchars($site['url_base']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>

        </div>
    </div>
</body>

</html>