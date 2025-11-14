<?php
$servername = "localhost";
$database = "saep_db";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabela users criada com sucesso.\n";
} else {
    echo "Erro ao criar tabela: " . mysqli_error($conn) . "\n";
}

$hashed_password = password_hash('admin', PASSWORD_DEFAULT);
$sql_insert = "INSERT INTO users (username, password) VALUES ('admin', '$hashed_password') ON DUPLICATE KEY UPDATE password='$hashed_password'";

if (mysqli_query($conn, $sql_insert)) {
    echo "Usuário admin inserido/atualizado com sucesso.\n";
} else {
    echo "Erro ao inserir usuário: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
