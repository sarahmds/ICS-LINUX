# Diagrama Entidade-Relacionamento — LojaInfoWeb

```mermaid
erDiagram
    PRODUTO ||--o{ PEDIDO : "possui"

    PRODUTO {
        int id PK
        varchar nome
        decimal valor_produto
    }

    PEDIDO {
        int id PK
        varchar nome
        int produto_id FK
        date data_pedido
        int quantidade
        decimal valor_total
    }
```

## Relacionamento

Um produto pode estar associado a vários pedidos.
Cada pedido referencia um produto por meio de `produto_id`,
que aponta para `Produto.id`.
