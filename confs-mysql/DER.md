# Diagrama Entidade-Relacionamento — LojaInfoWeb

```mermaid
erDiagram
    PEDIDO ||--|{ PEDIDO_ITEM : "contem"
    PRODUTO ||--o{ PEDIDO_ITEM : "aparece em"

    PRODUTO {
        int id PK
        varchar nome
        varchar imagem
        decimal valor_produto
    }

    PEDIDO {
        int id PK
        varchar nome
        datetime data_pedido
        decimal valor_total
        varchar status
    }

    PEDIDO_ITEM {
        int id PK
        int pedido_id FK
        int produto_id FK
        int quantidade
        decimal valor_unitario
    }
```

## Relacionamento

Um pedido contem um ou mais itens. Cada item referencia um produto e guarda
a quantidade e o preco unitario no momento da compra.

O campo `imagem` guarda a URL ou o caminho da imagem do produto. Novos pedidos recebem data e hora atuais e começam com o status `Pendente`.
