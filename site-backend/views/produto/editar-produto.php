<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../image-path.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    exit('ID de produto inválido.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $imagem = trim($_POST['imagem'] ?? '');
    $valor = filter_input(INPUT_POST, 'valor_produto', FILTER_VALIDATE_FLOAT);

    if ($nome === '' || $valor === false || $valor < 0 || !isValidImagePath($imagem)) {
        $erroEdicao = 'Informe um nome, preço e caminho de imagem válidos.';
    } else {
        $stmt = $connect->prepare("UPDATE Produto SET nome=?, imagem=?, valor_produto=? WHERE id=?");
        if (!$stmt) {
            error_log('Falha ao preparar atualização de produto: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível atualizar o produto.');
        }
        $stmt->bind_param("ssdi", $nome, $imagem, $valor, $id);
        if (!$stmt->execute()) {
            error_log('Falha ao atualizar produto: ' . $stmt->error);
            http_response_code(500);
            exit('Não foi possível atualizar o produto.');
        }

        header("Location: /?pagina=produtos&atualizado=1");
        exit;
    }
}

$stmt = $connect->prepare("SELECT * FROM Produto WHERE id=?");
if (!$stmt) {
    error_log('Falha ao preparar consulta de produto: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar o produto.');
}
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) {
    http_response_code(404);
    exit('Produto não encontrado.');
}
?>
<section class="admin-content">
    <div class="section-heading">
        <h2>Editar produto</h2>
        <p>Atualize os dados do item selecionado.</p>
    </div>
    <?php if (isset($erroEdicao)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erroEdicao, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" class="form-panel">
        <div class="mb-3">
            <label for="produto-nome" class="form-label">Nome</label>
            <input id="produto-nome" type="text" name="nome" class="form-control"
                   maxlength="100" value="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-3">
            <label for="produto-imagem" class="form-label">Imagem (URL pública ou caminho relativo à vitrine)</label>
            <input id="produto-imagem" type="text" name="imagem" class="form-control"
                   value="<?= htmlspecialchars($produto['imagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-3">
            <label for="produto-valor" class="form-label">Valor (R$)</label>
            <input id="produto-valor" type="number" step="0.01" name="valor_produto"
                   class="form-control" value="<?= $produto['valor_produto'] ?>" required>
        </div>

        <button class="btn btn-primary">Salvar</button>
        <a href="/?pagina=produtos" class="btn btn-outline-secondary">Cancelar</a>
    </form>
    </section>
