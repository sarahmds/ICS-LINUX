# Diagrama Entidade-Relacionamento — LojaInfoWeb

```mermaid
erDiagram
    PRODUTO ||--o{ PEDIDO : "possui"

    PRODUTO {
        int id PK
        varchar nome
        varchar imagem
        decimal valor_produto
    }

    PEDIDO {
        int id PK
        varchar nome
        int produto_id FK
        datetime data_pedido
        int quantidade
        decimal valor_total
        varchar status
    }
```

## Relacionamento

Um produto pode estar associado a vários pedidos.
Cada pedido referencia um produto por meio de `produto_id`,
que aponta para `Produto.id`.

O campo `imagem` guarda a URL ou o caminho da imagem do produto. Novos pedidos recebem data e hora atuais e começam com o status `Pendente`.
