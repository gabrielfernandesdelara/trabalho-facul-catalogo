<?php
require_once __DIR__ . '/../../banco/connect.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome && $email && $senha) {
        $conn = conectarBanco();

        // Verifica se o e-mail já está cadastrado
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensagem = '<div class="alert alert-danger">E-mail já cadastrado.</div>';
        } else {
            // Criptografa a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senhaHash);

            if ($stmt->execute()) {
                $mensagem = '<div class="alert alert-success">Usuário cadastrado com sucesso! <a href="login.php" class="alert-link">Faça login</a>.</div>';
            } else {
                $mensagem = '<div class="alert alert-danger">Erro ao cadastrar usuário.</div>';
            }
        }
        $stmt->close();
        $conn->close();
    } else {
        $mensagem = '<div class="alert alert-warning">Preencha todos os campos.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro - Porter Stranding</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/trabalho-facul-catalogo/assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/trabalho-facul-catalogo/assets/js/script.js"></script>
</head>
<body>
  <!-- Barra superior igual à home -->
  <div class="bg-light p-5 mb-4">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h1 class="display-4">Registro de Usuário</h1>
          <p class="lead">Crie sua conta para acessar o catálogo!</p>
        </div>
        <img src="/trabalho-facul-catalogo/assets/img/Icon.png" alt="Banner" class="banner-img" />
      </div>
    </div>
  </div>
  <!-- Formulário centralizado -->
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
      <h2 class="mb-3 text-center">Criar Conta</h2>
      <?php echo $mensagem; ?>
      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label for="nome" class="form-label">Nome</label>
          <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrar</button>
        <div class="mt-3 text-center">
          <a href="login.php" class="text-decoration-none">Já tem conta? Faça login</a>
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