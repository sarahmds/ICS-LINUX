# LojaInfoWeb — Controle de estoque e pedidos

Projeto acadêmico desenvolvido com PHP, Apache2, MariaDB e Bootstrap.

## Funcionalidades

- Cadastro, listagem, edição e exclusão de produtos.
- Cadastro, listagem, edição e exclusão de pedidos.
- Relacionamento entre pedidos e produtos por chave estrangeira.
- Painel administrativo separado do frontend.

## Arquitetura

- **Cliente:** navegador web.
- **Servidor WEB:** Debian Linux, Apache2 e PHP.
- **Servidor de banco de dados:** Debian Linux e MariaDB.
- **Frontend:** `site-frontend/`.
- **Backend administrativo:** `site-backend/`.

## Estrutura do repositório

- `confs-apache2/` — VirtualHosts do frontend e backend.
- `confs-mysql/` — esquema SQL e diagrama ER.
- `site-frontend/` — aplicação frontend.
- `site-backend/` — painel administrativo e operações CRUD.
- `apresentações/` — slides, capturas de tela e materiais da apresentação.

## Banco de dados

O arquivo `confs-mysql/schema.sql` documenta a criação do banco
e das tabelas `Produto` e `Pedido`.

O relacionamento é de um produto para vários pedidos.
A aplicação utiliza uma configuração local de conexão em
`site-backend/db.php`, que não deve ser publicada com credenciais reais.

## Configuração

Os arquivos de `confs-apache2/` são referências para configurar
os VirtualHosts no Apache. Revise os caminhos e os nomes de domínio
antes de utilizá-los em outro servidor.

## Testes realizados

- Apache: configuração validada com `apache2ctl configtest`.
- Frontend e backend: respostas HTTP 200 verificadas a partir do cliente.
- Produtos: operações CRUD testadas.
- Pedidos: cadastro, edição e exclusão testados.
- PHP: arquivos da aplicação verificados com `php -l`.

## Implantação em nuvem

A implantação em nuvem, o DNS público e o serviço de banco gerenciado
devem ser documentados separadamente após serem configurados e testados.
