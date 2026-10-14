<?php

$pagina = $_GET['pagina'] ?? 'inicio';
$titulo = match ($pagina) {
        'pedidos', 'editar-pedido', 'excluir-pedido' => 'Pedidos',
        'produtos', 'editar-produto', 'excluir-produto' => 'Produtos',
        default => 'Visão geral',
};

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin - lojainfoweb</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="admin.css">
</head>

<body>
        <header class="admin-header">
                <div class="admin-header-inner">
                        <a class="admin-brand" href="/" aria-label="LojaInfoWeb, início do painel">
                                <span class="brand-mark" aria-hidden="true">L</span>
                                <span>LojaInfoWeb<small>Painel administrativo</small></span>
                        </a>
                        <nav class="admin-nav" aria-label="Navegação principal">
                                <a href="/?pagina=pedidos" <?= in_array($pagina, ['pedidos', 'editar-pedido', 'excluir-pedido'], true) ? 'aria-current="page"' : '' ?>>Pedidos</a>
                                <a href="/?pagina=produtos" <?= in_array($pagina, ['produtos', 'editar-produto', 'excluir-produto'], true) ? 'aria-current="page"' : '' ?>>Produtos</a>
                        </nav>
                </div>
        </header>

        <main class="admin-main">
                <div class="admin-page-heading">
                        <p class="eyebrow">GESTÃO DA LOJA</p>
                        <h1><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>
                </div>

                <?php

                switch ($pagina) {

                        case 'pedidos':
                                require_once __DIR__ . '/views/pedido/pedidos.php';
                                break;

                        case 'produtos':
                                require_once __DIR__ . '/views/produto/produtos.php';
                                break;

                        case 'editar-produto':
                                require_once __DIR__ . '/views/produto/editar-produto.php';
                                break;

                        case 'excluir-produto':
                                require_once __DIR__ . '/views/produto/excluir-produto.php';
                                break;

                        case 'editar-pedido':
                                require_once __DIR__ . '/views/pedido/editar-pedido.php';
                                break;

                        case 'excluir-pedido':
                                require_once __DIR__ . '/views/pedido/excluir-pedido.php';
                                break;

                        default:
                                echo '<section class="welcome-panel"><h2>Bem-vindo ao painel</h2><p>Escolha uma área para acompanhar a operação da loja.</p><div class="welcome-links"><a class="btn btn-primary" href="/?pagina=pedidos">Acessar pedidos</a><a class="btn btn-outline-secondary" href="/?pagina=produtos">Gerenciar produtos</a></div></section>';
                                break;
                }

                ?>

        </main>

</body>
</html>
