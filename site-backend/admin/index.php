<?php
session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

$adminConfigPath = dirname(__DIR__) . '/config/admin.local.php';
if (!is_file($adminConfigPath)) {
        http_response_code(503);
        exit('Painel indisponível: configure as credenciais administrativas.');
}

$adminConfig = require $adminConfigPath;
$validAdminConfig = is_array($adminConfig)
        && isset($adminConfig['username'], $adminConfig['password_hash'])
        && is_string($adminConfig['username'])
        && $adminConfig['username'] !== ''
        && is_string($adminConfig['password_hash'])
        && password_get_info($adminConfig['password_hash'])['algo'] !== null;
if (!$validAdminConfig) {
        error_log('LojaInfoWeb: admin.local.php is missing valid credentials.');
        http_response_code(503);
        exit('Painel indisponível: configure as credenciais administrativas.');
}

$_SESSION['admin_csrf'] ??= bin2hex(random_bytes(32));
$csrfToken = $_SESSION['admin_csrf'];
$loginError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postedToken = $_POST['csrf_token'] ?? '';
        if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
                http_response_code(400);
                exit('Solicitação inválida. Atualize a página e tente novamente.');
        }

        if (isset($_POST['login'])) {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                if (
                        is_string($username)
                        && is_string($password)
                        && hash_equals($adminConfig['username'], $username)
                        && password_verify($password, $adminConfig['password_hash'])
                ) {
                        session_regenerate_id(true);
                        $_SESSION['admin_authenticated'] = true;
                        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
                        header('Location: /admin/', true, 303);
                        exit;
                }
                $loginError = 'Usuário ou senha inválidos.';
        } elseif (!empty($_SESSION['admin_authenticated']) && isset($_POST['logout'])) {
                $_SESSION = [];
                session_destroy();
                header('Location: /admin/', true, 303);
                exit;
        } elseif (empty($_SESSION['admin_authenticated'])) {
                http_response_code(400);
                exit('Solicitação inválida.');
        }
}

if (empty($_SESSION['admin_authenticated'])) {
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Acesso administrativo | LojaInfoWeb</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="/assets/css/admin.css">
        </head>
        <body>
                <main class="admin-main">
                        <section class="form-panel mx-auto" style="max-width: 28rem">
                                <h1>Acesso administrativo</h1>
                                <?php if ($loginError !== null): ?>
                                        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                                <form method="post" action="/admin/">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="mb-3">
                                                <label for="username" class="form-label">Usuário</label>
                                                <input id="username" name="username" class="form-control" autocomplete="username" required>
                                        </div>
                                        <div class="mb-3">
                                                <label for="password" class="form-label">Senha</label>
                                                <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" required>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="login" value="1">Entrar</button>
                                </form>
                        </section>
                </main>
        </body>
        </html>
        <?php
        exit;
}

require_once dirname(__DIR__) . '/db.php';

if (!defined('LOJAINFOWEB_ADMIN')) {
        define('LOJAINFOWEB_ADMIN', true);
}

function adminCsrfField(): string
{
        return '<input type="hidden" name="csrf_token" value="'
                . htmlspecialchars($_SESSION['admin_csrf'], ENT_QUOTES, 'UTF-8') . '">';
}

$titulos = [
        'inicio' => 'Visão geral',
        'pedidos' => 'Pedidos',
        'editar-pedido' => 'Pedidos',
        'excluir-pedido' => 'Pedidos',
        'produtos' => 'Produtos',
        'editar-produto' => 'Produtos',
        'excluir-produto' => 'Produtos',
];
$pagina = $_GET['pagina'] ?? 'inicio';
if (!is_string($pagina) || !array_key_exists($pagina, $titulos)) {
        http_response_code(404);
        $pagina = 'inicio';
}
$titulo = $titulos[$pagina];

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
        <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body>
        <header class="admin-header">
                <div class="admin-header-inner">
                        <a class="admin-brand" href="/admin/" aria-label="LojaInfoWeb, início do painel">
                                <span class="brand-mark" aria-hidden="true">L</span>
                                <span>LojaInfoWeb<small>Painel administrativo</small></span>
                        </a>
                        <nav class="admin-nav" aria-label="Navegação principal">
                                <a href="/admin/?pagina=pedidos" <?= in_array($pagina, ['pedidos', 'editar-pedido', 'excluir-pedido'], true) ? 'aria-current="page"' : '' ?>>Pedidos</a>
                                <a href="/admin/?pagina=produtos" <?= in_array($pagina, ['produtos', 'editar-produto', 'excluir-produto'], true) ? 'aria-current="page"' : '' ?>>Produtos</a>
                                <a href="/">Ver vitrine</a>
                                <form method="post" action="/admin/" class="d-inline">
                                        <?= adminCsrfField() ?>
                                        <button type="submit" name="logout" value="1" class="btn btn-sm btn-outline-light">Sair</button>
                                </form>
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
                                echo '<section class="welcome-panel"><h2>Bem-vindo ao painel</h2><p>Escolha uma área para acompanhar a operação da loja.</p><div class="welcome-links"><a class="btn btn-primary" href="/admin/?pagina=pedidos">Acessar pedidos</a><a class="btn btn-outline-secondary" href="/admin/?pagina=produtos">Gerenciar produtos</a></div></section>';
                                break;
                }

                ?>

        </main>

</body>
</html>
