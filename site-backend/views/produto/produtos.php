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
<section class="admin-content">
    <div class="section-heading">
        <h2>Produtos cadastrados</h2>
        <p>Consulte o catálogo e mantenha os valores atualizados.</p>
    </div>

    <!-- MENSAGENS -->
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'em_uso'): ?>
        <div class="alert alert-danger" role="alert">
            ⚠ Este produto está vinculado a um pedido e não pode ser excluído.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-success" role="status">
            ✅ Produto excluído com sucesso.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['criado'])): ?>
        <div class="alert alert-success" role="status">
            ✅ Produto cadastrado com sucesso.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['atualizado'])): ?>
        <div class="alert alert-success" role="status">Produto atualizado com sucesso.</div>
    <?php endif; ?>

    <div class="form-panel">
        <h2>Adicionar produto</h2>
        <form method="post">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-7">
                    <label for="produto-nome" class="form-label">Nome do produto</label>
                    <input id="produto-nome" type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-12 col-md-3">
                    <label for="produto-valor" class="form-label">Valor (R$)</label>
                    <input id="produto-valor" type="number" step="0.01" min="0" name="valor_produto" class="form-control" required>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-primary w-100">Salvar produto</button>
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
            <th>Valor</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($p = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td>R$ <?= number_format($p['valor_produto'], 2, ',', '.') ?></td>
                          <td class="table-actions">
                        <a href="?pagina=editar-produto&id=<?= $p['id'] ?>"
                              class="btn btn-outline-secondary btn-sm">Editar</a>

                        <a href="?pagina=excluir-produto&id=<?= $p['id'] ?>"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('Deseja realmente excluir este produto?')">
                            Excluir
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center text-secondary py-4">Nenhum produto cadastrado.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
