<?php
require_once __DIR__ . '/../../db.php';

/* =====================
   CADASTRAR PRODUTO
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $valor = $_POST['valor_produto'];

    $sql = "INSERT INTO Produto (nome, valor_produto) VALUES (?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sd", $nome, $valor);
    $stmt->execute();

    header("Location: /?pagina=produtos&criado=1");
    exit;
}

/* =====================
   LISTAR PRODUTOS
===================== */
$result = $connect->query("SELECT * FROM Produto");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <h2>Produtos</h2>

    <!-- MENSAGENS -->
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'em_uso'): ?>
        <div class="alert alert-danger">
            ⚠ Este produto está vinculado a um pedido e não pode ser excluído.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-success">
            ✅ Produto excluído com sucesso.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['criado'])): ?>
        <div class="alert alert-success">
            ✅ Produto cadastrado com sucesso.
        </div>
    <?php endif; ?>

    <!-- FORM CADASTRO -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Adicionar Produto</h5>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valor</label>
                    <input type="number" step="0.01" name="valor_produto" class="form-control" required>
                </div>

                <button class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>

    <!-- TABELA -->
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Valor</th>
            <th style="width: 180px;">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($p = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td>R$ <?= number_format($p['valor_produto'], 2, ',', '.') ?></td>
                    <td>
                        <a href="?pagina=editar-produto&id=<?= $p['id'] ?>"
                           class="btn btn-warning btn-sm">Editar</a>

                        <a href="?pagina=excluir-produto&id=<?= $p['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Deseja realmente excluir este produto?')">
                            Excluir
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Nenhum produto cadastrado.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>
