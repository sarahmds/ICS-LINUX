<?php
require_once __DIR__ . '/../../db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    exit('ID de pedido inválido.');
}

$statuses = ['Pendente', 'Em processamento', 'Enviado', 'Concluído', 'Cancelado'];
$erroEdicao = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    if (!is_string($status) || !in_array($status, $statuses, true)) {
        $erroEdicao = 'Selecione um status válido.';
    } else {
        $stmt = $connect->prepare("UPDATE Pedido SET status = ? WHERE id = ?");
        if (!$stmt) {
            error_log('Falha ao preparar atualização do pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível atualizar o pedido.');
        }
        $stmt->bind_param('si', $status, $id);
        if (!$stmt->execute()) {
            error_log('Falha ao atualizar pedido: ' . $stmt->error);
            http_response_code(500);
            exit('Não foi possível atualizar o pedido.');
        }

        header('Location: /?pagina=pedidos&atualizado=1');
        exit;
    }
}

$stmt = $connect->prepare("SELECT id, status FROM Pedido WHERE id = ?");
if (!$stmt) {
    error_log('Falha ao preparar consulta do pedido: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar o pedido.');
}
$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    error_log('Falha ao consultar pedido: ' . $stmt->error);
    http_response_code(500);
    exit('Não foi possível carregar o pedido.');
}
$pedido = $stmt->get_result()->fetch_assoc();
if (!$pedido) {
    http_response_code(404);
    exit('Pedido não encontrado.');
}
?>
<section class="admin-content">
    <div class="section-heading">
        <h2>Atualizar pedido #<?= (int) $pedido['id'] ?></h2>
        <p>Atualize o andamento da compra.</p>
    </div>
    <?php if ($erroEdicao !== null): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erroEdicao, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" class="form-panel">
        <div class="mb-3">
            <label for="pedido-status" class="form-label">Status</label>
            <select id="pedido-status" name="status" class="form-select" required>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $status === ($erroEdicao === null ? $pedido['status'] : ($_POST['status'] ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary">Salvar status</button>
        <a href="/?pagina=pedidos" class="btn btn-outline-secondary">Voltar aos pedidos</a>
    </form>
</section>
