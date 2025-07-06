<?php
// Inicia a sessão para poder acessar e destruir os dados do usuário logado
session_start();

// Destroi todas as informações da sessão, ou seja, faz o logout do usuário
session_destroy();

// Redireciona o usuário para a página de login após sair
header("Location: /trabalho-facul-catalogo/app/public/login.php");
exit;
?>