-- Execute uma unica vez em bancos existentes que foram criados pelo esquema anterior.
USE loja_db;

ALTER TABLE Produto
  ADD COLUMN imagem VARCHAR(500) DEFAULT NULL AFTER nome;

ALTER TABLE Pedido
  MODIFY COLUMN data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT 'Pendente';
