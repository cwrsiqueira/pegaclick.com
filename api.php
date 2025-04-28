<?php
define('PEGACLICK', true);
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$input = json_decode(file_get_contents("php://input"), true);

$token    = $input['token']    ?? null;
$pagina   = $input['pagina']   ?? null;
$evento   = $input['evento']   ?? null;
$elemento = $input['elemento'] ?? null;

if (!$token || !$pagina || !$evento) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados insuficientes.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM sites WHERE token_acesso = ?");
$stmt->execute([$token]);
$site = $stmt->fetch();

if (!$site) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Token inválido.']);
    exit;
}

$siteId = $site['id'];

// Se for evento de acesso, elemento será sempre 'pagina'
if ($evento === 'acesso') {
    $elemento = 'pagina';
}

// Busca ou cria elemento monitorado
$stmt = $pdo->prepare("SELECT id FROM elementos_monitorados WHERE site_id = ? AND nome_elemento = ?");
$stmt->execute([$siteId, $elemento]);
$el = $stmt->fetch();

if (!$el) {
    // Cadastrar elemento na hora se não existir
    $stmt = $pdo->prepare("INSERT INTO elementos_monitorados (site_id, nome_elemento, descricao) VALUES (?, ?, ?)");
    $descricao = ($evento === 'acesso') ? 'Monitoramento de acessos às páginas' : 'Elemento monitorado automaticamente';
    $stmt->execute([$siteId, $elemento, $descricao]);
    $elementoId = $pdo->lastInsertId();
} else {
    $elementoId = $el['id'];
}

// Inserir ou atualizar evento
$stmt = $pdo->prepare("
    INSERT INTO eventos (site_id, elemento_id, pagina, evento, quantidade)
    VALUES (?, ?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE
        quantidade = quantidade + 1,
        updated_at = CURRENT_TIMESTAMP
");
$stmt->execute([$siteId, $elementoId, $pagina, $evento]);

echo json_encode(['status' => 'ok']);
