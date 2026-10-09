<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Controle LojaInfoWeb</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">LojaInfoWeb - Controle de estoque</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Produto ID</th>
                <th>Data do Pedido</th>
                <th>Quantidade</th>
                <th>Valor Total</th>
            </tr>
        </thead>

        <tbody>
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include '../site-backend/db.php';

        $sql = "SELECT * FROM Pedido";
        $result = mysqli_query($connect, $sql);

        if (!$result) {
            die("Erro na query: " . mysqli_error($connect));
        }

        if (mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['produto_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['data_pedido']) . "</td>";
                echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                echo "<td>R$ " . htmlspecialchars($row['valor_total']) . "</td>";
                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='6'>Nenhum pedido encontrado.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
