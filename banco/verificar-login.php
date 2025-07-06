<?php
// Verifica se a sessão já foi iniciada; se não, inicia a sessão
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se o usuário está logado (se existe o id na sessão)
// Se não estiver logado, redireciona para a página de login e encerra o script
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /trabalho-facul-catalogo/app/public/login.php");
    exit;
}
?>