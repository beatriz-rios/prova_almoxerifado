<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/cadastroProdut.css">
</head>

<body class="bg-light">
    <!--NAV BAR-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-light me-2"
                            href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-dark me-2"
                            href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning me-2"
                            href="cadastroP.php">Cadastro
                            de Produto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-success me-2"
                            href="cadastroM.php">Cadastro
                            de Movimento</a>
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

    <!--FORMULÁRIO-->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Cadastro de Produto</h5>
                        <form method="post">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU:</label>
                                <input type="text" class="form-control" name="sku" required>
                            </div>
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria:</label>
                                <input type="text" class="form-control" name="categoria" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição:</label>
                                <textarea class="form-control" name="descricao" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="cor" class="form-label">Cor:</label>
                                <input type="text" class="form-control" name="cor">
                            </div>
                            <div class="mb-3">
                                <label for="unidade_medida" class="form-label">Unidade de Medida:</label>
                                <input type="text" class="form-control" name="unidade_medida">
                            </div>
                            <div class="mb-3">
                                <label for="data_criacao" class="form-label">Data de Criação:</label>
                                <input type="date" class="form-control" name="data_criacao">
                            </div>
                            <div class="mb-3">
                                <label for="textura" class="form-label">Textura:</label>
                                <input type="text" class="form-control" name="textura">
                            </div>
                            <div class="mb-3">
                                <label for="aplicacao" class="form-label">Aplicação:</label>
                                <input type="text" class="form-control" name="aplicacao">
                            </div>
                            <div class="mb-3">
                                <label for="estoque_minimo" class="form-label">Estoque Mínimo:</label>
                                <input type="number" step="0.01" class="form-control" name="estoque_minimo">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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