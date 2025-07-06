<?php
session_start();
require_once __DIR__ . '/../../banco/connect.php';
require_once __DIR__ . '/../../banco/verificar-login.php';

$usuario_nome = $_SESSION['usuario_nome'] ?? '';
$conn = conectarBanco();
$mensagem = "";
// CRIAR categoria
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nova_categoria'])) {
  $nome = trim($_POST['nome'] ?? '');
  if ($nome) {
    $stmt = $conn->prepare("INSERT INTO categorias (nome) VALUES (?)");
    $stmt->bind_param("s", $nome);
    if ($stmt->execute()) {
      $mensagem = '<div class="alert alert-success">Categoria criada!</div>';
    } else {
      $mensagem = '<div class="alert alert-danger">Erro ao criar categoria.</div>';
    }
    $stmt->close();
  } else {
    $mensagem = '<div class="alert alert-warning">Preencha o nome da categoria.</div>';
  }
}
// EDITAR categoria
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editar_categoria'])) {
  $id = intval($_POST['id']);
  $nome = trim($_POST['nome'] ?? '');
  if ($nome) {
    $stmt = $conn->prepare("UPDATE categorias SET nome=? WHERE id=?");
    $stmt->bind_param("si", $nome, $id);
    if ($stmt->execute()) {
      $mensagem = '<div class="alert alert-success">Categoria editada!</div>';
    } else {
      $mensagem = '<div class="alert alert-danger">Erro ao editar categoria.</div>';
    }
    $stmt->close();
  } else {
    $mensagem = '<div class="alert alert-warning">Preencha o nome da categoria.</div>';
  }
}
// EXCLUIR categoria
if (isset($_GET['excluir'])) {
  $id = intval($_GET['excluir']);
  $stmt = $conn->prepare("DELETE FROM categorias WHERE id=?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    $mensagem = '<div class="alert alert-success">Categoria excluída!</div>';
  } else {
    $mensagem = '<div class="alert alert-danger">Erro ao excluir categoria.</div>';
  }
  $stmt->close();
}
// BUSCAR categorias
$categorias = [];
$result = $conn->query("SELECT * FROM categorias ORDER BY id DESC");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $categorias[] = $row;
  }
}
// Se for editar, busca a categoria
$categoriaEditar = null;
if (isset($_GET['editar'])) {
  $id = intval($_GET['editar']);
  $stmt = $conn->prepare("SELECT * FROM categorias WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $categoriaEditar = $res->fetch_assoc();
  $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Categorias - Porter Stranding</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/trabalho-facul-catalogo/assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/trabalho-facul-catalogo/assets/js/script.js"></script>
</head>

<body>
  <!-- Barra superior igual à home -->
  <div class="bg-light p-5 mb-4 position-relative">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h1 class="display-4">Categorias</h1>
          <p class="lead">Gerencie as categorias do catálogo!</p>
          <nav>
            <ul class="nav mt-4">
              <li class="nav-item">
                <a class="nav-link active fw-bold" href="/trabalho-facul-catalogo/app/auth/home.php">Início</a>
              </li>
              <li>
                <a class="nav-link active fw-bold" href="/trabalho-facul-catalogo/app/auth/produtos.php">Produtos</a>
              </li>
              <li>
                <a class="nav-link active text-primary fw-bold"
                  href="/trabalho-facul-catalogo/app/auth/criar-produtos.php">Cadastro de produtos</a>
              </li>
            </ul>
          </nav>
        </div>
        <img src="/trabalho-facul-catalogo/assets/img/Icon.png" alt="Banner" class="banner-img" />
      </div>
      <?php if ($usuario_nome): ?>
        <div class="position-absolute top-0 end-0 mt-3 me-4 d-flex align-items-center gap-2">
          <span class="usuario-nome badge bg-primary fs-6 p-2">👤 <?php echo htmlspecialchars($usuario_nome); ?></span>
          <a href="/trabalho-facul-catalogo/app/public/logout.php" class="logout-link ms-2">Sair</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="container d-flex flex-column align-items-center" style="min-height: 60vh;">
    <?php echo $mensagem; ?>
    <!-- Botão criar categoria -->
    <a href="?nova=1" class="btn btn-primary mb-4" style="max-width: 250px;">Criar nova categoria</a>

    <!-- Formulário de criar categoria -->
    <?php if (isset($_GET['nova'])): ?>
      <div class="card mb-4" style="max-width: 600px; width: 100%;">
        <div class="card-body">
          <h5 class="card-title mb-3">Nova Categoria</h5>
          <form method="post">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome da categoria</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <button type="submit" name="nova_categoria" class="btn btn-primary">Criar</button>
            <!-- Botão cancelar padronizado -->
            <a href="criar-categorias.php" class="btn btn-cancelar ms-2">Cancelar</a>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- Formulário de editar categoria -->
    <?php if ($categoriaEditar): ?>
      <div class="card mb-4" style="max-width: 600px; width: 100%;">
        <div class="card-body">
          <h5 class="card-title mb-3">Editar Categoria</h5>
          <form method="post">
            <input type="hidden" name="id" value="<?php echo $categoriaEditar['id']; ?>">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome da categoria</label>
              <input type="text" class="form-control" id="nome" name="nome" required
                value="<?php echo htmlspecialchars($categoriaEditar['nome']); ?>">
            </div>
            <button type="submit" name="editar_categoria" class="btn btn-primary">Salvar</button>
            <!-- Botão cancelar padronizado -->
            <a href="criar-categorias.php" class="btn btn-cancelar ms-2">Cancelar</a>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- Lista de categorias -->
    <div class="card p-4 shadow" style="max-width: 600px; width: 100%;">
      <h2 class="mb-3 text-center">Lista de Categorias</h2>
      <?php if (count($categorias) > 0): ?>
        <ul class="list-group">
          <?php foreach ($categorias as $cat): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center py-2" style="font-size: 0.97rem;">
              <span><?php echo htmlspecialchars($cat['nome']); ?></span>
              <span>
                <a href="?editar=<?php echo $cat['id']; ?>" class="btn btn-sm btn-primary me-2 py-1 px-2"
                  style="font-size:0.9rem;">Editar</a>
                <!-- Botão excluir padronizado -->
                <a href="?excluir=<?php echo $cat['id']; ?>" class="btn btn-cancelar btn-sm py-1 px-2"
                  style="font-size:0.9rem;"
                  onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">Excluir</a>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="alert alert-warning text-center mt-3">Nenhuma categoria cadastrada.</div>
      <?php endif; ?>
    </div>
  </div>
  <!-- Rodapé -->
  <footer class="bg-primary text-white text-center py-3 mt-5">
    <div class="container">&copy; 2025 Porter Stranding.</div>
  </footer>
</body>

</html>