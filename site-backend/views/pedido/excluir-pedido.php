<?php
require_once __DIR__ . '/../../db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(400);
    exit('ID de pedido inválido.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $connect->prepare("SELECT id, nome FROM Pedido WHERE id = ?");
    if (!$stmt) {
        error_log('Falha ao preparar consulta de pedido para exclusão: ' . $connect->error);
        http_response_code(500);
        exit('Não foi possível carregar o pedido.');
    }
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        error_log('Falha ao consultar pedido para exclusão: ' . $stmt->error);
        http_response_code(500);
        exit('Não foi possível carregar o pedido.');
    }
    $pedido = $stmt->get_result()->fetch_assoc();

    if (!$pedido) {
        http_response_code(404);
        exit('Pedido não encontrado.');
    }
    ?>
    <section class="confirmation-panel">
        <h2>Confirmar exclusão</h2>
        <p>Deseja realmente excluir o pedido #<?= (int) $pedido['id'] ?> de
            <strong><?= htmlspecialchars($pedido['nome']) ?></strong>?
            Os itens do pedido também serão excluídos.
        </p>
        <form method="post" action="/?pagina=excluir-pedido&amp;id=<?= $id ?>">
            <button type="submit" class="btn btn-danger">Confirmar exclusão</button>
            <a href="/?pagina=pedidos" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </section>
    <?php
    exit;
}
$stmt = $connect->prepare("DELETE FROM Pedido WHERE id = ?");
if (!$stmt) {
    error_log('Falha ao preparar exclusão do pedido: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível excluir o pedido.');
}
$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    error_log('Falha ao excluir pedido: ' . $stmt->error);
    http_response_code(500);
    exit('Não foi possível excluir o pedido.');
}

header("Location: /?pagina=pedidos&excluido=1");
exit;
