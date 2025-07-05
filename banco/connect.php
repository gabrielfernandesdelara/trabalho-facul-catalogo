<?php
// Função que faz a conexão com o banco de dados MySQL
function conectarBanco() {
    // Dados para acessar o banco: servidor, usuário, senha e nome do banco
    $host = "localhost";
    $usuario = "root";
    $senha = "FiveBullet5!";
    $banco = "trabalho_facul_catalogo";

    // Cria a conexão usando os dados acima
    $conn = new mysqli($host, $usuario, $senha, $banco);

    // Se der algum erro na conexão, mostra a mensagem e para tudo
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Se deu tudo certo, retorna a conexão para ser usada no resto do código
    return $conn;
}
?>