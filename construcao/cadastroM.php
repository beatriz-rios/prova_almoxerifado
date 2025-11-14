<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
$servername = "localhost";
$database = "saep_db";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Movimentação de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">
    <!--NAV BAR-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-light me-2" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-dark me-2" href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning me-2" href="cadastroP.php">Cadastro de Produto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-success me-2" href="cadastroM.php">Cadastro de Movimento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary me-2" href="estoque.php">Estoque</a>
                    </li>

                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text">Olá, <?php echo $_SESSION['username']; ?>!</span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Sistema de Gerenciamento de Estoque Mínimo</h1>

<?php

        // Função para verificar alertas de estoque baixo
        function verificarAlertas($conn)
        {
            $alertas = [];
            $sql = "SELECT 
            idproduto,
            nome,
            quantidade,
            estoque_minimo FROM produto WHERE quantidade < estoque_minimo";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $alertas[] = $row;
            }
            return $alertas;
        }

        // Processar formulário de movimentação
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_movimentacao'])) {
            $produto_id = $_POST['produto_id'];
            $tipo = $_POST['tipo'];
            $quantidade = $_POST['quantidade'];
            $data_movimentacao = str_replace('T', ' ', $_POST['data_movimentacao']) . ':00';
            $observacao = $_POST['observacao'];

            // Verificar se há quantidade suficiente para saída
            if ($tipo == 'saida') {
                $sql_check = "SELECT quantidade FROM produto WHERE idproduto = $produto_id";
                $result_check = mysqli_query($conn, $sql_check);
                $row = mysqli_fetch_assoc($result_check);
                if ($row['quantidade'] < $quantidade) {
                    echo "<div class='alert'>Erro: Quantidade insuficiente em estoque. Disponível: " . $row['quantidade'] . "</div>";
                } else {
                    // Inserir movimentação
                    $sql_mov = "INSERT INTO movimentacao (produto_idproduto, tipo_entrada_saida, quantidade, data_movimentacao, observacao) VALUES ('$produto_id', '$tipo', '$quantidade', '$data_movimentacao', '$observacao')";
                    if (mysqli_query($conn, $sql_mov)) {

                        // Atualizar quantidade do produto
                        $sql_update = "UPDATE produto SET quantidade = quantidade - $quantidade WHERE idproduto = $produto_id";
                        if (mysqli_query($conn, $sql_update)) {
                            echo "<div class='success'>Movimentação registrada e estoque atualizado com sucesso!</div>";
                        } else {
                            echo "<div class='alert'>Movimentação registrada, mas erro ao atualizar estoque: " . mysqli_error($conn) . "</div>";
                        }
                    } else {
                        echo "<div class='alert'>Erro ao registrar movimentação: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {

                // Entrada
                $sql_mov = "INSERT INTO movimentacao
                          (produto_idproduto,
                          tipo_entrada_saida,
                          quantidade,
                          data_movimentacao,
                          observacao) 
                          VALUES (
                          '$produto_id',
                          '$tipo',
                          '$quantidade',
                          '$data_movimentacao',
                          '$observacao')";
                if (mysqli_query($conn, $sql_mov)) {

                    // Atualizar quantidade do produto
                    $sql_update = "UPDATE produto SET quantidade = quantidade + $quantidade WHERE idproduto = $produto_id";
                    if (mysqli_query($conn, $sql_update)) {
                        echo "<div class='success'>Movimentação registrada e estoque atualizado com sucesso!</div>";
                    } else {
                        echo "<div class='alert'>Movimentação registrada, mas erro ao atualizar estoque: " . mysqli_error($conn) . "</div>";
                    }
                } else {
                    echo "<div class='alert'>Erro ao registrar movimentação: " . mysqli_error($conn) . "</div>";
                }
            }
        }

        // Verificar alertas
        $alertas = verificarAlertas($conn);
        if (!empty($alertas)) {
            echo "<h2 class='mt-4'>Alertas de Estoque Baixo</h2>";
            foreach ($alertas as $alerta) {
                echo "<div class='alert alert-warning'>Produto: " . $alerta['nome'] . " - Quantidade atual: " . $alerta['quantidade'] . " - Mínimo: " . $alerta['estoque_minimo'] . "</div>";
            }
        }
        ?>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="card-title">Registrar Movimentação</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="produto_id" class="form-label">Produto:</label>
                        <select class="form-select" name="produto_id" id="produto_id" required>
                            <option value="">Selecione um produto</option>
                            <?php
                            $sql_produtos = "SELECT idproduto, nome FROM produto ORDER BY nome";
                            $result_produtos = mysqli_query($conn, $sql_produtos);
                            while ($produto = mysqli_fetch_assoc($result_produtos)) {
                                echo "<option value='" . $produto['idproduto'] . "'>" . $produto['nome'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Movimentação:</label>
                        <select class="form-select" name="tipo" id="tipo" required>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade:</label>
                        <input type="number" step="0.01" class="form-control" name="quantidade" id="quantidade" required>
                    </div>

                    <div class="mb-3">
                        <label for="data_movimentacao" class="form-label">Data da Movimentação:</label>
                        <input type="datetime-local" class="form-control" name="data_movimentacao" id="data_movimentacao"
                            value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="observacao" class="form-label">Observação:</label>
                        <textarea class="form-control" name="observacao" id="observacao" rows="3"></textarea>
                    </div>

                    <button type="submit" name="registrar_movimentacao" class="btn btn-primary">Registrar Movimentação</button>
                </form>
            </div>
        </div>

        <h2 class="mt-4">Histórico de Movimentações</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Data</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_historico = "SELECT
                                        m.idmovimentacao,
                                        p.nome,
                                        m.tipo_entrada_saida,
                                        m.quantidade,
                                        m.data_movimentacao,
                                        m.observacao
                                      FROM movimentacao m
                                      JOIN produto p ON m.produto_idproduto = p.idproduto
                                      ORDER BY m.data_movimentacao DESC";

                    $result_historico = mysqli_query($conn, $sql_historico);
                    while ($mov = mysqli_fetch_assoc($result_historico)) {
                        echo "<tr>";
                        echo "<td>" . $mov['idmovimentacao'] . "</td>";
                        echo "<td>" . $mov['nome'] . "</td>";
                        echo "<td>" . $mov['tipo_entrada_saida'] . "</td>";
                        echo "<td>" . $mov['quantidade'] . "</td>";
                        echo "<td>" . $mov['data_movimentacao'] . "</td>";
                        echo "<td>" . $mov['observacao'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php mysqli_close($conn); ?>
    </div>
</body>

</html>