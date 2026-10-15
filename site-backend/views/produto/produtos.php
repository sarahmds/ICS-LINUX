<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../image-path.php';

$erroCadastro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $imagem = trim($_POST['imagem'] ?? '');
    $valor = filter_input(INPUT_POST, 'valor_produto', FILTER_VALIDATE_FLOAT);

    if ($nome === '' || $valor === false || $valor < 0 || !isValidImagePath($imagem)) {
        $erroCadastro = 'Informe um nome, preço e caminho de imagem válidos.';
    } else {
        $stmt = $connect->prepare("INSERT INTO Produto (nome, imagem, valor_produto) VALUES (?, ?, ?)");
        if (!$stmt) {
            error_log('Falha ao preparar cadastro de produto: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível cadastrar o produto.');
        }
        $stmt->bind_param("ssd", $nome, $imagem, $valor);
        if (!$stmt->execute()) {
            error_log('Falha ao cadastrar produto: ' . $stmt->error);
            http_response_code(500);
            exit('Não foi possível cadastrar o produto.');
        }

        header("Location: /?pagina=produtos&criado=1");
        exit;
    }
}

$result = $connect->query("SELECT id, nome, imagem, valor_produto FROM Produto ORDER BY nome ASC");
if (!$result) {
    error_log('Falha ao consultar produtos: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar os produtos.');
}
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
    <?php if ($erroCadastro !== null): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erroCadastro, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['atualizado'])): ?>
        <div class="alert alert-success" role="status">Produto atualizado com sucesso.</div>
    <?php endif; ?>

    <div class="form-panel">
        <h2>Adicionar produto</h2>
        <form method="post">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label for="produto-nome" class="form-label">Nome do produto</label>
                    <input id="produto-nome" type="text" name="nome" class="form-control" maxlength="100" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="produto-imagem" class="form-label">Imagem (URL pública ou caminho relativo à vitrine)</label>
                    <input id="produto-imagem" type="text" name="imagem" class="form-control"
                           placeholder="imagens/produto.jpg ou https://..." required>
                </div>
                <div class="col-12 col-md-3">
                    <label for="produto-valor" class="form-label">Valor (R$)</label>
                    <input id="produto-valor" type="number" step="0.01" min="0" name="valor_produto" class="form-control" required>
                </div>
                <div class="col-12 col-md-3">
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
            <th>Imagem</th>
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
                    <td>
                        <?php if (!empty($p['imagem'])): ?>
                            <?php $imagemPreview = parse_url($p['imagem'], PHP_URL_SCHEME)
                                ? $p['imagem']
                                : 'http://lojainfowebifrn.com.br/' . ltrim($p['imagem'], '/'); ?>
                            <img src="<?= htmlspecialchars($imagemPreview, ENT_QUOTES, 'UTF-8') ?>"
                                 alt="" width="64" height="48" style="object-fit: cover">
                        <?php else: ?>
                            <span class="text-secondary">Sem imagem</span>
                        <?php endif; ?>
                    </td>
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
                <td colspan="5" class="text-center text-secondary py-4">Nenhum produto cadastrado.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
