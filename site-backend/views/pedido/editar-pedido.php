<?php
require_once __DIR__ . '/../../db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    exit('ID de pedido inválido.');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id  = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
    $data_pedido = $_POST['data_pedido'] ?? '';
    $quantidade  = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
    $valor_total = filter_input(INPUT_POST, 'valor_total', FILTER_VALIDATE_FLOAT);
    $status = $_POST['status'] ?? '';
    $statuses = ['Pendente', 'Em processamento', 'Enviado', 'Concluído', 'Cancelado'];

    if (!$produto_id || $produto_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $data_pedido)
        || !$quantidade || $quantidade <= 0 || $valor_total === false || $valor_total <= 0
        || !in_array($status, $statuses, true)) {
        $erroEdicao = 'Informe dados válidos para atualizar o pedido.';
    } else {
        $data_pedido = str_replace('T', ' ', $data_pedido) . ':00';
        $stmt = $connect->prepare("
            UPDATE Pedido
            SET produto_id=?, data_pedido=?, quantidade=?, valor_total=?, status=?
            WHERE id=?
        ");
        if (!$stmt) {
            error_log('Falha ao preparar atualização de pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível atualizar o pedido.');
        }
        $stmt->bind_param("isidsi", $produto_id, $data_pedido, $quantidade, $valor_total, $status, $id);
        if (!$stmt->execute()) {
            error_log('Falha ao atualizar pedido: ' . $stmt->error);
            http_response_code(500);
            exit('Não foi possível atualizar o pedido.');
        }

        header("Location: /?pagina=pedidos&atualizado=1");
        exit;
    }
}

$stmt = $connect->prepare("SELECT * FROM Pedido WHERE id=?");
if (!$stmt) {
    error_log('Falha ao preparar consulta de pedido: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar o pedido.');
}
$stmt->bind_param("i", $id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
if (!$pedido) {
    http_response_code(404);
    exit('Pedido não encontrado.');
}

$produtos = $connect->query("SELECT * FROM Produto");
if (!$produtos) {
    error_log('Falha ao consultar produtos para edição do pedido: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar os produtos.');
}
?>
<section class="admin-content">
    <div class="section-heading">
        <h2>Editar pedido</h2>
        <p>Atualize os dados e mantenha o produto associado ao pedido.</p>
    </div>
    <?php if (isset($erroEdicao)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erroEdicao, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" class="form-panel">
        <div class="mb-3">
            <label for="pedido-produto" class="form-label">Produto</label>
            <select id="pedido-produto" name="produto_id" class="form-select" required>
                <?php while ($p = $produtos->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $pedido['produto_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="pedido-data" class="form-label">Data do pedido</label>
            <input id="pedido-data" type="datetime-local" name="data_pedido" class="form-control"
                   value="<?= date('Y-m-d\TH:i', strtotime($pedido['data_pedido'])) ?>" required>
        </div>

        <div class="mb-3">
            <label for="pedido-quantidade" class="form-label">Quantidade</label>
            <input id="pedido-quantidade" type="number" name="quantidade" class="form-control" min="1"
                   value="<?= $pedido['quantidade'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="pedido-valor" class="form-label">Valor total (R$)</label>
            <input id="pedido-valor" type="number" step="0.01" name="valor_total" class="form-control" min="0.01"
                   value="<?= $pedido['valor_total'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="pedido-status" class="form-label">Status</label>
            <select id="pedido-status" name="status" class="form-select" required>
                <?php foreach (['Pendente', 'Em processamento', 'Enviado', 'Concluído', 'Cancelado'] as $status): ?>
                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $status === $pedido['status'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-primary">Salvar Alterações</button>
        <a href="/?pagina=pedidos" class="btn btn-outline-secondary">Cancelar</a>
    </form>
    </section>
