<?php
require_once __DIR__ . '/../../db.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) die("ID inválido");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id  = $_POST['produto_id'];
    $data_pedido = $_POST['data_pedido'];
    $quantidade  = $_POST['quantidade'];
    $valor_total = $_POST['valor_total'];

    $stmt = $connect->prepare("
        UPDATE Pedido
        SET produto_id=?, data_pedido=?, quantidade=?, valor_total=?
        WHERE id=?
    ");
    $stmt->bind_param("isidi", $produto_id, $data_pedido, $quantidade, $valor_total, $id);
    $stmt->execute();

    header("Location: /?pagina=pedidos&atualizado=1");
    exit;
}

$stmt = $connect->prepare("SELECT * FROM Pedido WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
if (!$pedido) die("Pedido não encontrado");

$produtos = $connect->query("SELECT * FROM Produto");
?>
<section class="admin-content">
    <div class="section-heading">
        <h2>Editar pedido</h2>
        <p>Atualize os dados e mantenha o produto associado ao pedido.</p>
    </div>
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
            <input id="pedido-data" type="date" name="data_pedido" class="form-control"
                   value="<?= $pedido['data_pedido'] ?>" required>
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

        <button class="btn btn-primary">Salvar Alterações</button>
        <a href="/?pagina=pedidos" class="btn btn-outline-secondary">Cancelar</a>
    </form>
    </section>
