-- Execute uma unica vez depois de migracao-loja.sql em bancos existentes.
-- Esta migracao separa os itens em PedidoItem e remove a estrutura antiga de Produto
-- de Pedido (produto_id, quantidade e fk_pedido_produto).
USE loja_db;

CREATE TABLE IF NOT EXISTS PedidoItem (
  id INT NOT NULL AUTO_INCREMENT,
  pedido_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  valor_unitario DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_pedido_item_pedido (pedido_id),
  KEY idx_pedido_item_produto (produto_id),
  CONSTRAINT fk_pedido_item_pedido
    FOREIGN KEY (pedido_id) REFERENCES Pedido(id) ON DELETE CASCADE,
  CONSTRAINT fk_pedido_item_produto
    FOREIGN KEY (produto_id) REFERENCES Produto(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO PedidoItem (pedido_id, produto_id, quantidade, valor_unitario)
SELECT id, produto_id, quantidade, valor_total / quantidade
FROM Pedido
WHERE NOT EXISTS (SELECT 1 FROM PedidoItem);

ALTER TABLE Pedido
  DROP FOREIGN KEY fk_pedido_produto,
  DROP KEY fk_pedido_produto,
  DROP COLUMN produto_id,
  DROP COLUMN quantidade;
