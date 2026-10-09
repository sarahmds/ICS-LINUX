<?php
require_once __DIR__ . '/../../db.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $valor = $_POST['valor_produto'];

    $sql = "UPDATE Produto SET nome=?, valor_produto=? WHERE id=?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sdi", $nome, $valor, $id);
    $stmt->execute();

    header("Location: /?pagina=produtos");
    exit;
}

$stmt = $connect->prepare("SELECT * FROM Produto WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) die("Produto não encontrado");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Produto</h2>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control"
                   value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Valor</label>
            <input type="number" step="0.01" name="valor_produto"
                   class="form-control" value="<?= $produto['valor_produto'] ?>" required>
        </div>

        <button class="btn btn-primary">Salvar</button>
        <a href="/?pagina=produtos" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
