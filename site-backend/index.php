<?php

$pagina = $_GET['pagina'] ?? 'inicio';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
        <meta charset="utf-8">
        <title>Admin - lojainfoweb</title>
        <link rel="stylesheet" href="../backend.css">
</head>

<body>

        <h1>Admin - lojainfoweb</h1>

        <nav>
                <a href="?pagina=pedidos">Pedidos</a>
                <a href="?pagina=produtos">Produtos</a>
        </nav>

        <main>

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
                                echo '<h2>Bem-vindo ao painel administrativo</h2>';
                                echo '<p>Selecione uma opção acima.</p>';
                                break;
                }

                ?>

        </main>

</body>
</html>
