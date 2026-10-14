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

    header("Location: /?pagina=produtos&atualizado=1");
    exit;
}

$stmt = $connect->prepare("SELECT * FROM Produto WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) die("Produto não encontrado");
?>
<section class="admin-content">
    <div class="section-heading">
        <h2>Editar produto</h2>
        <p>Atualize os dados do item selecionado.</p>
    </div>
    <form method="post" class="form-panel">
        <div class="mb-3">
            <label for="produto-nome" class="form-label">Nome</label>
            <input id="produto-nome" type="text" name="nome" class="form-control"
                   value="<?= htmlspecialchars($produto['nome']) ?>" required>
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
