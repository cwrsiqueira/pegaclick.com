<?php
// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=pegaclick;charset=utf8mb4", 'root', '', [
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

$stmt = $pdo->prepare("SELECT id, monitorar_acesso FROM sites WHERE token_acesso = ?");
$stmt->execute([$token]);
$site = $stmt->fetch();

if (!$site) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Token inválido.']);
    exit;
}

$siteId = $site['id'];

// Definir elemento_id
if ($evento === 'acesso') {
    if (!$site['monitorar_acesso']) {
        // Se não monitora acessos, ignorar
        http_response_code(204);
        exit;
    }

    // Buscar o elemento 'pagina'
    $stmt = $pdo->prepare("SELECT id FROM elementos_monitorados WHERE site_id = ? AND nome_elemento = ?");
    $stmt->execute([$siteId, 'pagina']);
    $el = $stmt->fetch();

    if (!$el) {
        http_response_code(204);
        exit;
    }

    $elementoId = $el['id'];
} else {
    // Evento de click em elementos monitorados
    $stmt = $pdo->prepare("SELECT id FROM elementos_monitorados WHERE site_id = ? AND nome_elemento = ?");
    $stmt->execute([$siteId, $elemento]);
    $el = $stmt->fetch();

    if (!$el) {
        http_response_code(204);
        exit;
    }

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
