# LojaInfoWeb

Projeto acadêmico de loja virtual e controle administrativo, desenvolvido com PHP, Apache e MariaDB/MySQL.

## Aplicação principal

- `site-frontend/` — catálogo público de produtos e listagem de pedidos.
- `site-backend/` — painel administrativo e CRUD de produtos e pedidos.
- `confs-mysql/` — esquema e documentação do relacionamento do banco principal.
- `confs-apache2/` — VirtualHosts de referência para os dois sites.

O Apache aponta para `site-frontend/` e `site-backend/`; mantenha esses diretórios nesses caminhos ao implantar. O painel usa autenticação Basic configurada no Apache do ambiente implantado. Preserve essa configuração e os arquivos de autenticação locais, sem adicioná-los ao Git.

## Banco da aplicação principal

`confs-mysql/schema.sql` documenta o banco `loja_db` e as tabelas `Produto` e `Pedido`. Um produto pode estar associado a vários pedidos por meio de `Pedido.produto_id`.

Para importar o esquema, use `mysql -u <usuario> -p < confs-mysql/schema.sql` em uma instância MariaDB/MySQL.

A aplicação inclui a configuração local de conexão por `site-backend/db.php`. Esse arquivo é ignorado pelo Git e deve conter somente credenciais locais; não publique seus valores.

## Outros arquivos

- `examples/php-mysql/` — exemplo independente de CRUD para a tabela `people`; não faz parte da loja principal.
- `docs/deployment-notes.md` — anotações do ambiente de implantação.
- `docs/screenshots/` — capturas dos servidores web e de banco.

## Estrutura

```text
.
├── confs-apache2/
├── confs-mysql/
├── docs/
│   ├── deployment-notes.md
│   └── screenshots/
├── examples/
│   └── php-mysql/
│       ├── back/
│       ├── database/schema.sql
│       ├── front/
│       └── README.md
├── site-backend/
└── site-frontend/
```

## Validação

Verifique a configuração do Apache com `apache2ctl configtest` e a sintaxe dos arquivos PHP com `php -l`. Testes de CRUD dependem do PHP com `mysqli`, do banco configurado e do arquivo local de conexão.
