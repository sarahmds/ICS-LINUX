<?php
session_start();
require_once __DIR__ . '/../site-backend/db.php';

$_SESSION['cart'] ??= [];
if (!isset($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(32));
}

function redirectToCart(): void
{
    header('Location: /?pagina=carrinho', true, 303);
    exit;
}

function setCartNotice(string $type, string $message): void
{
    $_SESSION['cart_notice'] = ['type' => $type, 'message' => $message];
}

$page = $_GET['pagina'] ?? 'loja';
if (!is_string($page) || !in_array($page, ['loja', 'carrinho'], true)) {
    http_response_code(404);
    $page = 'loja';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['cart_token'], $token)) {
        http_response_code(400);
        exit('Sessão inválida. Atualize a página e tente novamente.');
    }

    $action = $_POST['acao'] ?? '';
    if ($action === 'adicionar') {
        $productId = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0 || !$quantity || $quantity <= 0 || $quantity > 9999) {
            setCartNotice('error', 'Selecione uma quantidade válida.');
            redirectToCart();
        }

        $stmt = $connect->prepare("SELECT id FROM Produto WHERE id = ?");
        if (!$stmt) {
            error_log('Falha ao preparar consulta do produto para o carrinho: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível adicionar o produto ao carrinho.');
        }
        $stmt->bind_param('i', $productId);
        if (!$stmt->execute()) {
            error_log('Falha ao verificar produto para o carrinho: ' . $stmt->error);
            http_response_code(500);
            exit('Não foi possível adicionar o produto ao carrinho.');
        }
        if (!$stmt->get_result()->fetch_assoc()) {
            setCartNotice('error', 'Esse produto não está mais disponível.');
            redirectToCart();
        }

        $_SESSION['cart'][$productId] = min(
            9999,
            ($_SESSION['cart'][$productId] ?? 0) + $quantity
        );
        setCartNotice('success', 'Produto adicionado ao carrinho.');
        redirectToCart();
    }

    if ($action === 'atualizar') {
        $quantities = $_POST['quantidades'] ?? [];
        if (!is_array($quantities)) {
            http_response_code(400);
            exit('Dados inválidos para atualizar o carrinho.');
        }

        foreach ($_SESSION['cart'] as $productId => $currentQuantity) {
            if (isset($_POST['remover']) && (string) $_POST['remover'] === (string) $productId) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }

            $quantity = filter_var($quantities[$productId] ?? null, FILTER_VALIDATE_INT);
            if ($quantity === false || $quantity === null || $quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId] = min(9999, $quantity);
            }
        }
        setCartNotice('success', 'Carrinho atualizado.');
        redirectToCart();
    }

    if ($action === 'finalizar') {
        $name = $_POST['nome'] ?? null;
        if (!is_string($name) || trim($name) === '' || strlen(trim($name)) > 100) {
            setCartNotice('error', 'Informe seu nome para finalizar a compra.');
            redirectToCart();
        }
        if ($_SESSION['cart'] === []) {
            setCartNotice('error', 'Adicione pelo menos um produto antes de finalizar a compra.');
            redirectToCart();
        }

        $cart = $_SESSION['cart'];
        $productIds = array_map('intval', array_keys($cart));
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $productQuery = $connect->prepare(
            "SELECT id, valor_produto FROM Produto WHERE id IN ($placeholders) FOR UPDATE"
        );
        if (!$productQuery) {
            error_log('Falha ao preparar consulta dos produtos do pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }

        $bindArguments = [str_repeat('i', count($productIds))];
        foreach ($productIds as &$productId) {
            $bindArguments[] = &$productId;
        }
        unset($productId);
        call_user_func_array([$productQuery, 'bind_param'], $bindArguments);

        if (!$connect->begin_transaction()) {
            error_log('Falha ao iniciar transação do pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }

        if (!$productQuery->execute()) {
            $connect->rollback();
            error_log('Falha ao carregar produtos do pedido: ' . $productQuery->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }

        $products = [];
        $result = $productQuery->get_result();
        while ($product = $result->fetch_assoc()) {
            $products[(int) $product['id']] = $product;
        }
        if (count($products) !== count($cart)) {
            $connect->rollback();
            setCartNotice('error', 'Um ou mais produtos não estão mais disponíveis. Revise seu carrinho.');
            redirectToCart();
        }

        $totalCents = 0;
        foreach ($cart as $productId => $quantity) {
            $totalCents += (int) round((float) $products[$productId]['valor_produto'] * 100) * $quantity;
        }
        $total = number_format($totalCents / 100, 2, '.', '');
        $name = trim($name);
        $order = $connect->prepare(
            "INSERT INTO Pedido (nome, data_pedido, valor_total, status) VALUES (?, CURRENT_TIMESTAMP, ?, 'Pendente')"
        );
        if (!$order) {
            $connect->rollback();
            error_log('Falha ao preparar pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }
        $order->bind_param('sd', $name, $total);
        if (!$order->execute()) {
            $connect->rollback();
            error_log('Falha ao gravar pedido: ' . $order->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }

        $orderId = $connect->insert_id;
        $item = $connect->prepare(
            "INSERT INTO PedidoItem (pedido_id, produto_id, quantidade, valor_unitario) VALUES (?, ?, ?, ?)"
        );
        if (!$item) {
            $connect->rollback();
            error_log('Falha ao preparar itens do pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }

        foreach ($cart as $productId => $quantity) {
            $unitPrice = $products[$productId]['valor_produto'];
            $item->bind_param('iiid', $orderId, $productId, $quantity, $unitPrice);
            if (!$item->execute()) {
                $connect->rollback();
                error_log('Falha ao gravar item do pedido: ' . $item->error);
                http_response_code(500);
                exit('Não foi possível finalizar a compra.');
            }
        }

        if (!$connect->commit()) {
            $connect->rollback();
            error_log('Falha ao confirmar transação do pedido: ' . $connect->error);
            http_response_code(500);
            exit('Não foi possível finalizar a compra.');
        }

        $_SESSION['cart'] = [];
        $_SESSION['order_success'] = true;
        header('Location: /?pagina=carrinho', true, 303);
        exit;
    }

    http_response_code(400);
    exit('Ação inválida.');
}

$orderSuccess = $_SESSION['order_success'] ?? false;
unset($_SESSION['order_success']);
$notice = $_SESSION['cart_notice'] ?? null;
unset($_SESSION['cart_notice']);

$productsResult = $connect->query(
    "SELECT id, nome, imagem, valor_produto FROM Produto ORDER BY nome ASC"
);
if (!$productsResult) {
    error_log('Falha ao carregar catálogo: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar o catálogo. Tente novamente mais tarde.');
}
$products = [];
while ($product = $productsResult->fetch_assoc()) {
    $products[(int) $product['id']] = $product;
}

$cartItems = [];
$cartTotalCents = 0;
foreach ($_SESSION['cart'] as $productId => $quantity) {
    if (!isset($products[$productId])) {
        unset($_SESSION['cart'][$productId]);
        continue;
    }
    $product = $products[$productId];
    $product['quantidade'] = $quantity;
    $cartItems[] = $product;
    $cartTotalCents += (int) round((float) $product['valor_produto'] * 100) * $quantity;
}
$cartCount = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page === 'carrinho' ? 'Carrinho' : 'Catálogo' ?> | LojaInfoWeb</title>
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
        <a href="/#produtos">Produtos</a>
        <a href="/?pagina=carrinho" aria-current="<?= $page === 'carrinho' ? 'page' : 'false' ?>">
            Carrinho <span class="cart-count"><?= (int) $cartCount ?></span>
        </a>
        <a href="http://lojainfowebifrn.backend.biz/">Painel administrativo</a>
    </nav>
</header>

<main id="inicio">
    <?php if ($notice): ?>
        <div class="purchase-message <?= $notice['type'] === 'error' ? 'purchase-error' : '' ?>" role="<?= $notice['type'] === 'error' ? 'alert' : 'status' ?>">
            <?= htmlspecialchars($notice['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($page === 'carrinho'): ?>
        <section class="cart-section">
            <?php if ($orderSuccess): ?>
                <div class="checkout-success" role="status">
                    <span class="checkout-success-icon" aria-hidden="true">✓</span>
                    <h1>Compra finalizada</h1>
                </div>
            <?php else: ?>
                <div class="section-heading cart-heading">
                    <div>
                        <p class="eyebrow">SUA COMPRA</p>
                        <h1>Carrinho</h1>
                        <p class="cart-subtitle">
                            <?= (int) $cartCount ?> <?= $cartCount === 1 ? 'item' : 'itens' ?> no seu carrinho
                        </p>
                    </div>
                    <a class="btn btn-outline-secondary" href="/#produtos">Continuar comprando</a>
                </div>

                <?php if ($cartItems === []): ?>
                <p class="empty-state">Seu carrinho está vazio. Explore o catálogo e adicione produtos para começar.</p>
                <?php else: ?>
                <form method="post" action="/?pagina=carrinho">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['cart_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="acao" value="atualizar">
                    <div class="cart-list">
                        <?php foreach ($cartItems as $item): ?>
                            <article class="cart-item">
                                <?php if (!empty($item['imagem'])): ?>
                                    <img class="cart-item-image" src="<?= htmlspecialchars($item['imagem'], ENT_QUOTES, 'UTF-8') ?>"
                                         alt="<?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php else: ?>
                                    <div class="cart-item-image product-image-placeholder">Imagem indisponível</div>
                                <?php endif; ?>
                                <div class="cart-item-info">
                                    <h2><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
                                    <p class="cart-item-unit-price">R$ <?= number_format((float) $item['valor_produto'], 2, ',', '.') ?> <span>por unidade</span></p>
                                </div>
                                <label class="cart-quantity">
                                    <span>Quantidade</span>
                                    <input type="number" class="form-control" min="0" max="9999"
                                           name="quantidades[<?= (int) $item['id'] ?>]"
                                           value="<?= (int) $item['quantidade'] ?>">
                                </label>
                                <strong class="cart-line-total">
                                    R$ <?= number_format(((int) round((float) $item['valor_produto'] * 100) * $item['quantidade']) / 100, 2, ',', '.') ?>
                                </strong>
                                <button class="remove-item" type="submit" name="remover"
                                        value="<?= (int) $item['id'] ?>">Remover</button>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-actions">
                        <button class="btn btn-outline-secondary" type="submit">Atualizar carrinho</button>
                    </div>
                </form>

                <section class="checkout-panel">
                    <div class="checkout-summary">
                        <p class="eyebrow">TOTAL DA COMPRA</p>
                        <p class="checkout-total">R$ <?= number_format($cartTotalCents / 100, 2, ',', '.') ?></p>
                        <p class="checkout-note">O valor final considera as quantidades selecionadas.</p>
                    </div>
                    <form method="post" action="/?pagina=carrinho" class="checkout-form">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['cart_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="acao" value="finalizar">
                        <label for="cliente-nome">Seu nome</label>
                        <input id="cliente-nome" type="text" name="nome" class="form-control"
                               maxlength="100" autocomplete="name" required>
                        <button class="btn btn-primary checkout-submit" type="submit">Finalizar compra</button>
                    </form>
                </section>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    <?php else: ?>
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
                <span class="item-count"><?= count($products) ?> itens</span>
            </div>
            <?php if ($products !== []): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <article class="product-item">
                            <?php if (!empty($product['imagem'])): ?>
                                <img class="product-image"
                                     src="<?= htmlspecialchars($product['imagem'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars($product['nome'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php else: ?>
                                <div class="product-image-placeholder" role="img"
                                     aria-label="Imagem indisponível para <?= htmlspecialchars($product['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                    Imagem indisponível
                                </div>
                            <?php endif; ?>
                            <span class="product-id">ITEM <?= (int) $product['id'] ?></span>
                            <h2><?= htmlspecialchars($product['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <p class="product-price">R$ <?= number_format((float) $product['valor_produto'], 2, ',', '.') ?></p>
                            <form class="purchase-form" method="post" action="/?pagina=carrinho">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['cart_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="acao" value="adicionar">
                                <input type="hidden" name="produto_id" value="<?= (int) $product['id'] ?>">
                                <label for="quantidade-<?= (int) $product['id'] ?>">Quantidade</label>
                                <input id="quantidade-<?= (int) $product['id'] ?>" type="number" name="quantidade"
                                       class="form-control" min="1" max="9999" value="1" required>
                                <button class="btn btn-primary" type="submit">Adicionar ao carrinho</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-state">Ainda não há produtos cadastrados. Volte em breve para conferir o catálogo.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<footer class="store-footer">
    LojaInfoWeb <span>Catálogo e gestão em um só lugar.</span>
</footer>
</body>
</html>
