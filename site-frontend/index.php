<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../site-backend/db.php';

$produtos_result = mysqli_query($connect, "SELECT id, nome, valor_produto FROM Produto ORDER BY nome ASC");
$pedidos_result = mysqli_query($connect, "SELECT * FROM Pedido ORDER BY data_pedido DESC");

if (!$produtos_result || !$pedidos_result) {
    http_response_code(500);
    die('Não foi possível carregar os dados da loja. Tente novamente mais tarde.');
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
    <a class="store-brand" href="#inicio"><span class="brand-mark">L</span>LojaInfoWeb</a>
    <nav aria-label="Navegação principal">
        <a href="#produtos">Produtos</a>
        <a href="#pedidos">Pedidos</a>
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

    <section class="catalog-section" id="produtos">
        <div class="section-heading">
            <div>
                <p class="eyebrow">CATÁLOGO</p>
                <h2>Produtos disponíveis</h2>
            </div>
            <span class="item-count"><?= mysqli_num_rows($produtos_result) ?> itens</span>
        </div>
        <?php if (mysqli_num_rows($produtos_result) > 0): ?>
            <div class="product-grid">
                <?php while ($produto = mysqli_fetch_assoc($produtos_result)): ?>
                    <article class="product-item">
                        <span class="product-id">ITEM <?= htmlspecialchars($produto['id']) ?></span>
                        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                        <p class="product-price">R$ <?= number_format((float) $produto['valor_produto'], 2, ',', '.') ?></p>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">Ainda não há produtos cadastrados.</p>
        <?php endif; ?>
    </section>

    <section class="orders-section" id="pedidos">
        <div class="section-heading">
            <div>
                <p class="eyebrow">ACOMPANHAMENTO</p>
                <h2>Pedidos registrados</h2>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Produto ID</th>
                        <th>Data do pedido</th>
                        <th>Quantidade</th>
                        <th>Valor total</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($pedidos_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($pedidos_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= htmlspecialchars($row['produto_id']) ?></td>
                            <td><?= htmlspecialchars($row['data_pedido']) ?></td>
                            <td><?= htmlspecialchars($row['quantidade']) ?></td>
                            <td>R$ <?= number_format((float) $row['valor_total'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4">Nenhum pedido encontrado.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer class="store-footer">LojaInfoWeb <span>Catálogo e gestão em um só lugar.</span></footer>
</body>
</html>
