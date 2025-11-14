<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Movimentação de Estoque</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Sistema de Gerenciamento de Estoque Mínimo</h1>

        <?php
        $servername = "localhost";
        $database = "saep_db";
        $username = "root";
        $password = "";

        $conn = mysqli_connect($servername, $username, $password, $database);
        if (!$conn) {
            die("Falha na conexão: " . mysqli_connect_error());
        }

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
            echo "<h2>Alertas de Estoque Baixo</h2>";
            foreach ($alertas as $alerta) {
                echo "<div class='alert'>Produto: " . $alerta['nome'] . " - Quantidade atual: " . $alerta['quantidade'] . " - Mínimo: " . $alerta['estoque_minimo'] . "</div>";
            }
        }
        ?>

        <h2>Registrar Movimentação</h2>
        <form method="post">
            <label for="produto_id">Produto:</label>
            <select name="produto_id" id="produto_id" required>
                <option value="">Selecione um produto</option>
                <?php
                $sql_produtos = "SELECT idproduto, nome FROM produto ORDER BY nome";
                $result_produtos = mysqli_query($conn, $sql_produtos);
                while ($produto = mysqli_fetch_assoc($result_produtos)) {
                    echo "<option value='" . $produto['idproduto'] . "'>" . $produto['nome'] . "</option>";
                }
                ?>
            </select>

            <label for="tipo">Tipo de Movimentação:</label>
            <select name="tipo" id="tipo" required>
                <option value="entrada">Entrada</option>
                <option value="saida">Saída</option>
            </select>

            <label for="quantidade">Quantidade:</label>
            <input type="number" step="0.01" name="quantidade" id="quantidade" required>

            <label for="data_movimentacao">Data da Movimentação:</label>
            <input type="datetime-local" name="data_movimentacao" id="data_movimentacao"
                value="<?php echo date('Y-m-d\TH:i'); ?>" required>

            <label for="observacao">Observação:</label>
            <textarea name="observacao" id="observacao"></textarea>

            <button type="submit" name="registrar_movimentacao">Registrar Movimentação</button>
        </form>

        <h2>Histórico de Movimentações</h2>
        <table>
            <thead>
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

        <h2>Estoque Atual</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Quantidade</th>
                    <th>Mínimo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_estoque = "SELECT idproduto, nome, quantidade, estoque_minimo FROM produto ORDER BY nome";
                $result_estoque = mysqli_query($conn, $sql_estoque);
                while ($prod = mysqli_fetch_assoc($result_estoque)) {
                    $status = ($prod['quantidade'] < $prod['estoque_minimo']) ? 'Abaixo do mínimo' : 'OK';
                    $status_class = ($prod['quantidade'] < $prod['estoque_minimo']) ? 'alert' : '';
                    echo "<tr>";
                    echo "<td>" . $prod['idproduto'] . "</td>";
                    echo "<td>" . $prod['nome'] . "</td>";
                    echo "<td>" . $prod['quantidade'] . "</td>";
                    echo "<td>" . $prod['estoque_minimo'] . "</td>";
                    echo "<td class='$status_class'>" . $status . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <?php mysqli_close($conn); ?>
    </div>
</body>

</html>