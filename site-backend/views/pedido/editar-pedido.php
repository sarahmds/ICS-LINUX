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

    header("Location: /?pagina=pedidos");
    exit;
}

$stmt = $connect->prepare("SELECT * FROM Pedido WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
if (!$pedido) die("Pedido não encontrado");

$produtos = $connect->query("SELECT * FROM Produto");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <a href="/?pagina=pedidos" class="btn btn-secondary mb-3">← Voltar</a>

    <h2>Editar Pedido</h2>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Produto</label>
            <select name="produto_id" class="form-control" required>
                <?php while ($p = $produtos->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $pedido['produto_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Data do Pedido</label>
            <input type="date" name="data_pedido" class="form-control"
                   value="<?= $pedido['data_pedido'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" name="quantidade" class="form-control"
                   value="<?= $pedido['quantidade'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Valor Total</label>
            <input type="number" step="0.01" name="valor_total" class="form-control"
                   value="<?= $pedido['valor_total'] ?>" required>
        </div>

        <button class="btn btn-primary">Salvar Alterações</button>
    </form>

</div>
</body>
</html>
