<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $url  = trim($_POST['url']);
    $monitorar_acesso = isset($_POST['monitorar_acesso']) ? 1 : 0;

    if ($nome && $url) {
        $token = bin2hex(random_bytes(16));

        $stmt = $pdo->prepare("INSERT INTO sites (usuario_id, nome_site, url_base, token_acesso, monitorar_acesso) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $nome, $url, $token, $monitorar_acesso]);
        $siteId = $pdo->lastInsertId();

        // Se optou por monitorar acessos, cria o elemento "pagina"
        if ($monitorar_acesso) {
            $stmt = $pdo->prepare("INSERT INTO elementos_monitorados (site_id, nome_elemento, descricao) VALUES (?, ?, ?)");
            $stmt->execute([$siteId, 'pagina', 'Monitoramento de Acesso à Página']);
        }

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Site</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h1 class="mb-4">Cadastrar Novo Site</h1>

        <form method="POST" class="card p-4 bg-white">
            <div class="mb-3">
                <label class="form-label">Nome do Site</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">URL Base do Site</label>
                <input type="url" name="url" class="form-control" placeholder="https://exemplo.com" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="monitorar_acesso" id="monitorarAcesso" checked>
                <label class="form-check-label" for="monitorarAcesso">
                    Monitorar Acessos às Páginas
                </label>
            </div>

            <button type="submit" class="btn btn-success">Cadastrar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>