<?php
if (!defined('LOJAINFOWEB_ADMIN')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 3) . '/db.php';
require_once __DIR__ . '/../../image-path.php';

$erroCadastro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeInput = $_POST['nome'] ?? '';
    $imageInput = $_POST['imagem'] ?? '';
    $nome = is_string($nomeInput) ? trim($nomeInput) : '';
    $imagem = is_string($imageInput) ? trim($imageInput) : '';
    $nomeLength = preg_match_all('/./us', $nome);
    $valor = filter_input(INPUT_POST, 'valor_produto', FILTER_VALIDATE_FLOAT);

    if ($nome === '' || $nomeLength === false || $nomeLength > 100 || $valor === false || $valor === null || !is_finite($valor) || $valor < 0 || !isValidImagePath($imagem)) {
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

        header("Location: /admin/?pagina=produtos&criado=1");
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
        <form method="post" action="/admin/?pagina=produtos">
            <?= adminCsrfField() ?>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label for="produto-nome" class="form-label">Nome do produto</label>
                    <input id="produto-nome" type="text" name="nome" class="form-control" maxlength="100" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="produto-imagem" class="form-label">Imagem (URL pública ou caminho relativo à vitrine)</label>
                    <input id="produto-imagem" type="text" name="imagem" class="form-control"
                                   placeholder="/assets/img/produto.jpg ou https://..." required>
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
                    <td><?= (int) $p['id'] ?></td>
                    <td>
                        <?php if (!empty($p['imagem'])): ?>
                            <?php $imagemPreview = parse_url($p['imagem'], PHP_URL_SCHEME)
                                ? $p['imagem']
                                : '/' . ltrim($p['imagem'], '/'); ?>
                            <img src="<?= htmlspecialchars($imagemPreview, ENT_QUOTES, 'UTF-8') ?>"
                                 alt="" width="64" height="48" style="object-fit: cover">
                        <?php else: ?>
                            <span class="text-secondary">Sem imagem</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>R$ <?= number_format($p['valor_produto'], 2, ',', '.') ?></td>
                    <td class="table-actions">
                        <a href="?pagina=editar-produto&id=<?= $p['id'] ?>"
                              class="btn btn-outline-secondary btn-sm">Editar</a>

                        <form method="post" action="/admin/?pagina=excluir-produto&amp;id=<?= (int) $p['id'] ?>"
                              onsubmit="return confirm('Deseja realmente excluir este produto?')">
                            <?= adminCsrfField() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">Excluir</button>
                        </form>
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
