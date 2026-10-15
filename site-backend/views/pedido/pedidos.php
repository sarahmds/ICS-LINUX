<?php
require_once __DIR__ . '/../../db.php';

$result = $connect->query("
    SELECT Pedido.id, Pedido.nome, Pedido.data_pedido, Pedido.valor_total, Pedido.status,
           COALESCE(SUM(PedidoItem.quantidade), 0) AS quantidade_itens,
           GROUP_CONCAT(
               CONCAT(Produto.nome, ' (', PedidoItem.quantidade, ')')
               ORDER BY Produto.nome SEPARATOR ', '
           ) AS produtos
    FROM Pedido
    LEFT JOIN PedidoItem ON PedidoItem.pedido_id = Pedido.id
    LEFT JOIN Produto ON Produto.id = PedidoItem.produto_id
    GROUP BY Pedido.id
    ORDER BY Pedido.data_pedido DESC, Pedido.id DESC
");
if (!$result) {
    error_log('Falha ao consultar pedidos: ' . $connect->error);
    http_response_code(500);
    exit('Não foi possível carregar os pedidos.');
}
?>
<section class="admin-content">
    <div class="section-heading">
        <h2>Pedidos recentes</h2>
        <p>Consulte os itens e acompanhe o status de cada compra.</p>
    </div>

    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-success" role="status">Pedido excluído com sucesso.</div>
    <?php endif; ?>
    <?php if (isset($_GET['atualizado'])): ?>
        <div class="alert alert-success" role="status">Status do pedido atualizado.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Produtos</th>
                    <th>Data e hora</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= (int) $pedido['id'] ?></td>
                        <td><?= htmlspecialchars($pedido['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars($pedido['produtos'] ?? 'Sem itens', ENT_QUOTES, 'UTF-8') ?>
                            <span class="text-secondary">(<?= (int) $pedido['quantidade_itens'] ?> unidades)</span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
                        <td>R$ <?= number_format((float) $pedido['valor_total'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($pedido['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="table-actions">
                            <a href="/?pagina=editar-pedido&amp;id=<?= (int) $pedido['id'] ?>"
                               class="btn btn-outline-secondary btn-sm">Atualizar status</a>
                            <a href="/?pagina=excluir-pedido&amp;id=<?= (int) $pedido['id'] ?>"
                               class="btn btn-outline-danger btn-sm">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">Nenhum pedido cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
