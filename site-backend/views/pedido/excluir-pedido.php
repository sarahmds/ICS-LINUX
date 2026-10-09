<?php
require_once __DIR__ . '/../../db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(400);
    exit('ID de pedido inválido.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $connect->prepare(
        "SELECT id, nome FROM Pedido WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pedido = $stmt->get_result()->fetch_assoc();

    if (!$pedido) {
        http_response_code(404);
        exit('Pedido não encontrado.');
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Confirmar exclusão</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <h2>Confirmar exclusão</h2>
        <p>Deseja realmente excluir o pedido
            <strong><?= htmlspecialchars($pedido['nome']) ?></strong>?
        </p>
        <form method="post" action="/?pagina=excluir-pedido&amp;id=<?= $id ?>">
            <button type="submit" class="btn btn-danger">Confirmar exclusão</button>
            <a href="/?pagina=pedidos" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$stmt = $connect->prepare("DELETE FROM Pedido WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: /?pagina=pedidos&excluido=1");
exit;
$stmt = $connect->prepare("DELETE FROM Pedido WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: /?pagina=pedidos&excluido=1");
exit;
