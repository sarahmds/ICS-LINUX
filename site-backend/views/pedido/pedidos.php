<?php
require_once __DIR__ . '/../../db.php';

// Verifica banco de dados correto
$database = $connect->query("SELECT DATABASE()")->fetch_row()[0];
if ($database !== 'loja_db') {
    die("Erro: Conectado no banco errado: $database");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome        = trim($_POST['nome'] ?? '');
    $produto_id  = isset($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
    $data_pedido = $_POST['data_pedido'] ?? '';
    $quantidade  = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 0;
    $valor_total = isset($_POST['valor_total']) ? (float) $_POST['valor_total'] : 0.0;

    // VALIDAÇÃO
    if ($nome === '') die("Informe o nome do pedido.");
    if ($produto_id <= 0) die("Selecione um produto válido.");
    if (!$data_pedido) die("Informe a data do pedido.");
    if ($quantidade <= 0) die("Informe uma quantidade válida.");
    if ($valor_total <= 0) die("Informe um valor total válido.");

    $stmt = $connect->prepare("
        INSERT INTO Pedido (nome, produto_id, data_pedido, quantidade, valor_total)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sisid", $nome, $produto_id, $data_pedido, $quantidade, $valor_total);
    $stmt->execute();

    header("Location: /?pagina=pedidos&criado=1");
    exit;
}




$result = $connect->query("
    SELECT Pedido.*, Produto.nome AS nome_produto
    FROM Pedido
    JOIN Produto ON Pedido.produto_id = Produto.id
    ORDER BY Pedido.data_pedido DESC
");


$produtos_result = $connect->query("SELECT * FROM Produto ORDER BY nome ASC");
$produtos = [];
while ($p = $produtos_result->fetch_assoc()) {
    $produtos[] = $p;
}
?>

<section class="admin-content">
    <div class="section-heading">
        <h2>Pedidos recentes</h2>
        <p>Registre pedidos e acompanhe seus produtos, quantidades e valores.</p>
    </div>

    <!-- MENSAGENS -->
    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-success" role="status">Pedido excluído com sucesso.</div>
    <?php endif; ?>
    <?php if (isset($_GET['criado'])): ?>
        <div class="alert alert-success" role="status">Pedido cadastrado com sucesso.</div>
    <?php endif; ?>
    <?php if (isset($_GET['atualizado'])): ?>
        <div class="alert alert-success" role="status">Pedido atualizado com sucesso.</div>
    <?php endif; ?>

    <!-- FORM ADICIONAR PEDIDO -->
    <div class="form-panel">
        <h2>Adicionar pedido</h2>
        <form method="post">
            <input type="hidden" name="acao" value="adicionar">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="pedido-nome" class="form-label">Nome do pedido</label>
                    <input id="pedido-nome" type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="pedido-produto" class="form-label">Produto</label>
                    <select id="pedido-produto" name="produto_id" class="form-select" required>
                        <option value="">Selecione um produto</option>
                        <?php foreach ($produtos as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="pedido-data" class="form-label">Data do pedido</label>
                    <input id="pedido-data" type="date" name="data_pedido" class="form-control" required>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="pedido-quantidade" class="form-label">Quantidade</label>
                    <input id="pedido-quantidade" type="number" name="quantidade" class="form-control" min="1" required>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="pedido-valor" class="form-label">Valor total (R$)</label>
                    <input id="pedido-valor" type="number" step="0.01" name="valor_total" class="form-control" min="0.01" required>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Salvar pedido</button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Produto</th>
                <th>Data</th>
                <th>Quantidade</th>
                <th>Valor Total</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['nome_produto']) ?></td>
                    <td><?= $row['data_pedido'] ?></td>
                    <td><?= $row['quantidade'] ?></td>
                    <td>R$ <?= number_format($row['valor_total'], 2, ',', '.') ?></td>
                    <td class="table-actions">
                        <a href="/?pagina=editar-pedido&id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm">Editar</a>
                        <a href="/?pagina=excluir-pedido&id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center text-secondary py-4">Nenhum pedido cadastrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
