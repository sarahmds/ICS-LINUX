<?php
require_once __DIR__ . '/../../db.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID inválido");
}

/* Verifica se o produto está em algum pedido */
$stmt = $connect->prepare(
    "SELECT COUNT(*) AS total FROM PedidoItem WHERE produto_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['total'] > 0) {
    // Produto em uso  NÃO exclui
    header("Location: /?pagina=produtos&erro=em_uso");
    exit;
}

/*  Não está em pedido  pode excluir */
$stmt = $connect->prepare("DELETE FROM Produto WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: /?pagina=produtos&excluido=1");
exit;
