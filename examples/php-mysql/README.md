# Exemplo PHP e MySQL

Exemplo independente de CRUD para a tabela `people`, separado da aplicação LojaInfoWeb.

## Arquivos

- `back/` — páginas PHP de listagem, cadastro, detalhes, edição e remoção.
- `front/simple.min.css` — folha de estilos usada pelo exemplo.
- `database/schema.sql` — criação do banco e da tabela do exemplo.

## Execução

1. Importe `database/schema.sql` em uma instância MariaDB/MySQL.
2. Configure as variáveis `DB_HOST`, `DB_USER`, `DB_PASS` e `DB_NAME` no ambiente PHP, se os valores padrão de desenvolvimento não forem adequados.
3. Configure temporariamente o document root do servidor PHP para `examples/php-mysql/` e abra `/back/`.

O cabeçalho da aplicação carrega a folha de estilos de `../front/simple.min.css`. Não use a configuração de banco deste exemplo para a aplicação principal.
