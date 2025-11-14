<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = "localhost";
    $database = "saep_db";
    $username = "root";
    $password = "";

    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!$conn) {
        die("Falha na conexão: " . mysqli_connect_error());
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Check if username already exists
    $sql_check = "SELECT id FROM users WHERE username = '$user'";
    $result_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        $error = "Usuário já existe.";
    } else {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password) VALUES ('$user', '$hashed_password')";
        if (mysqli_query($conn, $sql)) {
            $success = "Usuário registrado com sucesso. Faça login.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Erro ao registrar: " . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Registrar</h5>
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuário:</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha:</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrar</button>
                        </form>
                        <p class="mt-3 text-center"><a href="login.php">Já tem conta? Faça login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
