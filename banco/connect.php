<?php
// Função para conectar ao banco de dados MySQL
function conectarBanco() {
    $host = "localhost";
    $usuario = "root";
    $senha = "FiveBullet5!";
    $banco = "trabalho_facul_catalogo";

    $conn = new mysqli($host, $usuario, $senha, $banco);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    return $conn;
}
?>