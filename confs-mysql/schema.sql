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
  produto_id INT NOT NULL,
  data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  quantidade INT NOT NULL,
  valor_total DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Pendente',
  PRIMARY KEY (id),
  KEY fk_pedido_produto (produto_id),
  CONSTRAINT fk_pedido_produto
    FOREIGN KEY (produto_id) REFERENCES Produto(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
