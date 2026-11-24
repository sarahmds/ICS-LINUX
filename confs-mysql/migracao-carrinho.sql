-- Execute uma unica vez depois de migracao-loja.sql em bancos existentes.
USE loja_db;

CREATE TABLE PedidoItem (
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
FROM Pedido;

ALTER TABLE Pedido
  DROP FOREIGN KEY fk_pedido_produto,
  DROP KEY fk_pedido_produto,
  DROP COLUMN produto_id,
  DROP COLUMN quantidade;
