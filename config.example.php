<?php
if (!defined('PEGACLICK')) {
    http_response_code(403);
    exit('Página não encontrada.');
}

session_start();

$environment = 'local'; // 'local' ou 'servidor'

if ($environment === 'local') {
    // Configurações do Banco de Dados LOCAL
    $host = 'localhost';
    $db   = 'pegaclick';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
} else {
    // Configurações do Banco de Dados SERVIDOR
    $host = 'localhost';
    $db   = 'pegaclick';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
}

// Conexão PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}

// Função para verificar login
function verificaLogin()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}
