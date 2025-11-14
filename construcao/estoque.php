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
    <title>Estoque Atual</title>
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
        <h1 class="text-center mb-4">Estoque Atual</h1>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
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
                        $status_class = ($prod['quantidade'] < $prod['estoque_minimo']) ? 'table-danger' : 'table-success';
                        echo "<tr class='$status_class'>";
                        echo "<td>" . $prod['idproduto'] . "</td>";
                        echo "<td>" . $prod['nome'] . "</td>";
                        echo "<td>" . $prod['quantidade'] . "</td>";
                        echo "<td>" . $prod['estoque_minimo'] . "</td>";
                        echo "<td>" . $status . "</td>";
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
