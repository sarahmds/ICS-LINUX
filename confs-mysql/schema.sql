-- LojaInfoWeb
-- Estrutura do banco de dados.
-- Este arquivo cria tabelas apenas se elas ainda não existirem.
-- Não contém usuários, senhas ou dados de clientes.

CREATE DATABASE IF NOT EXISTS loja_db
  CHARACTER SET utf8mb4;

USE loja_db;

CREATE TABLE IF NOT EXISTS Produto (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  imagem VARCHAR(500) DEFAULT NULL,
  valor_produto DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS Pedido (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  valor_total DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Pendente',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
