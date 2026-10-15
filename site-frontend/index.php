<?php
session_start();
require_once __DIR__ . '/../site-backend/db.php';

$erroCompra = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? null;
    $produtoId = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);

    if (!is_string($nome) || trim($nome) === '' || !$produtoId || $produtoId <= 0
        || !$quantidade || $quantidade <= 0) {
        http_response_code(400);
        $erroCompra = 'Informe seu nome e selecione um produto e uma quantidade válidos.';
    } else {
        $nome = trim($nome);
        $stmt = $connect->prepare("
            INSERT INTO Pedido (nome, produto_id, data_pedido, quantidade, valor_total, status)
            SELECT ?, id, CURRENT_TIMESTAMP, ?, valor_produto * ?, 'Pendente'
            FROM Produto
            WHERE id = ?
        ");
        if (!$stmt) {
            error_log('Falha ao preparar pedido da loja: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível registrar seu pedido.');
        }
        $stmt->bind_param("siii", $nome, $quantidade, $quantidade, $produtoId);
        if (!$stmt->execute()) {
            error_log('Falha ao registrar pedido da loja: ' . $stmt->error);
            http_response_code(500);
            exit('Não foi possível registrar seu pedido.');
        }

        if ($stmt->affected_rows !== 1) {
            http_response_code(400);
            $erroCompra = 'Esse produto não está mais disponível. Atualize o catálogo e tente novamente.';
        } else {
            $_SESSION['pedido_confirmado'] = $connect->insert_id;
            header('Location: /', true, 303);
            exit;
        }
    }
}

$pedidoConfirmado = $_SESSION['pedido_confirmado'] ?? null;
unset($_SESSION['pedido_confirmado']);

$produtos_result = $connect->query(
    "SELECT id, nome, imagem, valor_produto FROM Produto ORDER BY nome ASC"
);
if (!$produtos_result) {
    error_log('Falha ao carregar catálogo: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar o catálogo. Tente novamente mais tarde.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LojaInfoWeb | Catálogo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="store.css" rel="stylesheet">
</head>
<body>
<header class="store-header">
    <a class="store-brand" href="/"><span class="brand-mark">L</span>LojaInfoWeb</a>
    <nav aria-label="Navegação principal">
        <a href="#produtos">Produtos</a>
        <a href="http://lojainfowebifrn.backend.biz/">Painel administrativo</a>
    </nav>
</header>

<main id="inicio">
    <section class="store-hero">
        <div class="hero-copy">
            <p class="eyebrow">TECNOLOGIA PARA O DIA A DIA</p>
            <h1>Boas escolhas começam por um bom catálogo.</h1>
            <p>Explore os produtos disponíveis na LojaInfoWeb.</p>
            <a class="btn btn-light" href="#produtos">Explorar produtos</a>
        </div>
        <div class="hero-index" aria-hidden="true"><span>LOJA</span><strong>01</strong></div>
    </section>

    <?php if ($pedidoConfirmado): ?>
        <section class="purchase-message" role="status">
            <h2>Pedido confirmado!</h2>
            <p>Seu número de pedido é <strong>#<?= (int) $pedidoConfirmado ?></strong>. Status: Pendente.</p>
        </section>
    <?php endif; ?>
    <?php if ($erroCompra !== null): ?>
        <div class="purchase-message purchase-error" role="alert">
            <?= htmlspecialchars($erroCompra, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section class="catalog-section" id="produtos">
        <div class="section-heading">
            <div>
                <p class="eyebrow">CATÁLOGO</p>
                <h2>Produtos disponíveis</h2>
            </div>
            <span class="item-count"><?= $produtos_result->num_rows ?> itens</span>
        </div>
        <?php if ($produtos_result->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($produto = $produtos_result->fetch_assoc()): ?>
                    <article class="product-item">
                        <?php if (!empty($produto['imagem'])): ?>
                            <img class="product-image"
                                 src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                            <div class="product-image-placeholder" role="img"
                                 aria-label="Imagem indisponível para <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                Imagem indisponível
                            </div>
                        <?php endif; ?>
                        <span class="product-id">ITEM <?= (int) $produto['id'] ?></span>
                        <h3><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="product-price">R$ <?= number_format((float) $produto['valor_produto'], 2, ',', '.') ?></p>
                        <form class="purchase-form" method="post" action="/">
                            <input type="hidden" name="produto_id" value="<?= (int) $produto['id'] ?>">
                            <label for="cliente-<?= (int) $produto['id'] ?>">Seu nome</label>
                            <input id="cliente-<?= (int) $produto['id'] ?>" type="text" name="nome"
                                   class="form-control" maxlength="100" autocomplete="name" required>
                            <label for="quantidade-<?= (int) $produto['id'] ?>">Quantidade</label>
                            <input id="quantidade-<?= (int) $produto['id'] ?>" type="number" name="quantidade"
                                   class="form-control" min="1" value="1" required>
                            <button class="btn btn-primary" type="submit">Confirmar compra</button>
                        </form>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">Ainda não há produtos cadastrados. Volte em breve para conferir o catálogo.</p>
        <?php endif; ?>
    </section>
</main>

<footer class="store-footer">
    LojaInfoWeb <span>Catálogo e gestão em um só lugar.</span>
</footer>
</body>
</html>
