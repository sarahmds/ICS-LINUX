<?php
if (!defined('LOJAINFOWEB_ADMIN')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 3) . '/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || $id <= 0) {
    http_response_code(400);
    exit('Solicitação inválida.');
}

$stmt = $connect->prepare(
    "SELECT COUNT(*) AS total FROM PedidoItem WHERE produto_id = ?"
);
if (!$stmt) {
    error_log('Falha ao preparar verificação de uso do produto: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível excluir o produto.');
}
$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    error_log('Falha ao verificar uso do produto: ' . $stmt->error);
    http_response_code(500);
    exit('Não foi possível excluir o produto.');
}
$result = $stmt->get_result()->fetch_assoc();

if ($result['total'] > 0) {
    header("Location: /admin/?pagina=produtos&erro=em_uso");
    exit;
}

$stmt = $connect->prepare("DELETE FROM Produto WHERE id = ?");
if (!$stmt) {
    error_log('Falha ao preparar exclusão do produto: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível excluir o produto.');
}
$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    error_log('Falha ao excluir produto: ' . $stmt->error);
    http_response_code(500);
    exit('Não foi possível excluir o produto.');
}

header("Location: /admin/?pagina=produtos&excluido=1");
exit;
