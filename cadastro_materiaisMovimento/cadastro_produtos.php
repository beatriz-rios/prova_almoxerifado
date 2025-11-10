<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    
</head>

<body>

    <!--NAV BAR-->
    <ul>
        <li class menu><a href="http://127.0.0.1:5500/menu/menu.html">Menu</a></li>
        <li class menu><a href="http://127.0.0.1:5500/login/index.html">Login</a></li>
        <li class menu><a href="http://127.0.0.1:5500/cadastro_materiais/cadastro.html">Cadastro</a></li>
        <li class menu><a href="">Estoque</a></li>
    </ul>


    <!--DIV DO QUADRADO GRANDE-->
    <div class="cadastro">
        <form method="post">
            <!--INPUTS PARA ADICIONAR-->
            Nome:<input type="text" name="nome"><br>
            SKU:<input type="text" name="sku"><br>
            Categoria:<input type="text" name="categoria"><br>
            Descrição:<input type="text" name="descricao"><br>
            Cor:<input type="text" name="cor"><br>
            Unidade de Medida:<input type="text" name="unidade_medida"><br>
            Data de Criação:<input type="date" name="data_criacao"><br>
            Textura:<input type="text" name="textura"><br>
            Aplicação:<input type="text" name="aplicacao"><br>
            Estoque Mínimo:<input type="number" step="0.01" name="estoque_minimo"><br>

            <input type="submit" value="Enviar">
        </form>
    </div>

    <?php
    $servername = "localhost";
    $database = "saep_db";
    $username = "root";
    $password = "";

    $conn = mysqli_connect(
        $servername,
        $username,
        $password,
        $database
    );
    if (!$conn) {
        die("Falha na conexão: " . mysqli_connect_error());
    }
    echo "<p>Conectado com Sucesso</p>";


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome = $_POST['nome'];
        $sku = $_POST['sku'];
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
        $cor = $_POST['cor'];
        $unidade_medida = $_POST['unidade_medida'];
        $data_criacao = $_POST['data_criacao'];
        $textura = $_POST['textura'];
        $aplicacao = $_POST['aplicacao'];
        $estoque_minimo = $_POST['estoque_minimo'];

        $sql = "insert into produto (
        nome,
        sku,
        categoria,
        descricao,
        cor,
        unidade_medida,
        data_criacao,
        textura,
        aplicacao,
        estoque_minimo
        )
        values(
        '$nome',
        '$sku',
        '$categoria',
        '$descricao',
        '$cor',
        '$unidade_medida',
        '$data_criacao',
        '$textura',
        '$aplicacao',
        '$estoque_minimo'
        ); ";

        if (mysqli_query($conn, $sql)) {
            echo "<p class='text'>Comando executado com sucesso</p>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
    }

    ?>
</body>

</html>