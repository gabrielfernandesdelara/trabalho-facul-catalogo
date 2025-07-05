<?php
// Inicia a sessão para manter o login do usuário
session_start();
require_once __DIR__ . '/../../banco/connect.php';
// Variável para mensagem de retorno
$mensagem = "";
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Pega os dados do formulário e remove espaços extras
  $email = trim($_POST['email'] ?? '');
  $senha = $_POST['senha'] ?? '';
  // Verifica se todos os campos foram preenchidos
  if ($email && $senha) {
    $conn = conectarBanco();
    // Prepara a consulta para buscar o usuário pelo e-mail
    $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    // Se encontrou o usuário, verifica a senha
    if ($stmt->num_rows === 1) {
      $stmt->bind_result($id, $nome, $senhaHash);
      $stmt->fetch();
      // Verifica se a senha está correta
      if (password_verify($senha, $senhaHash)) {
        // Salva os dados do usuário na sessão e redireciona para a home
        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario_nome'] = $nome;
        header("Location: /trabalho-facul-catalogo/app/auth/home.php");
        exit;
      } else {
        // Senha incorreta
        $mensagem = '<div class="alert alert-danger">Senha incorreta.</div>';
      }
    } else {
      // E-mail não encontrado
      $mensagem = '<div class="alert alert-danger">E-mail não encontrado.</div>';
    }
    $stmt->close();
    $conn->close();
  } else {
    // Se algum campo não foi preenchido, mostra aviso
    $mensagem = '<div class="alert alert-warning">Preencha todos os campos.</div>';
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Porter Stranding</title>
  <!-- Importa o Bootstrap e o CSS customizado -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/trabalho-facul-catalogo/assets/css/style.css" />
  <script src="/trabalho-facul-catalogo/assets/js/script.js"></script>
</head>

<body>
  <!-- Barra superior igual à home -->
  <div class="bg-light p-5 mb-4">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h1 class="display-4">Login</h1>
          <p class="lead">Acesse sua conta para ver o catálogo!</p>
        </div>
        <img src="/trabalho-facul-catalogo/assets/img/Icon.png" alt="Banner" class="banner-img" />
      </div>
    </div>
  </div>
  <!-- Formulário centralizado -->
  <div class="container d-flex justify-content-center align-items-center min-vh-60">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
      <h2 class="mb-3 text-center">Entrar</h2>
      <?php echo $mensagem; ?>
      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <div class="mt-3 text-center">
          <a href="registro.php" class="text-decoration-none">Não tem conta? Cadastre-se</a>
        </div>
      </form>
    </div>
  </div>
  <!-- Rodapé -->
  <footer class="bg-primary text-white text-center py-3 mt-5">
    <div class="container">&copy; 2025 Porter Stranding.</div>
  </footer>
</body>

</html>