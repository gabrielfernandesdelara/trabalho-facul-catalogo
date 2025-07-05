<?php
// Inicia a sessão para manter o login do usuário
session_start();
require_once '../../banco/connect.php';
// Pega o nome do usuário logado, se houver
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
$conn = conectarBanco();
// Consulta para buscar produtos com a categoria e o nome do usuário que cadastrou
$sql = "SELECT p.*, c.nome AS categoria_nome, u.nome AS usuario_nome
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Produtos - Porter Stranding</title>
  <!-- Importa o Bootstrap e o CSS customizado -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/trabalho-facul-catalogo/assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/trabalho-facul-catalogo/assets/js/script.js"></script>
</head>

<body>
  <!-- Barra superior igual à home, sem o botão Produtos -->
  <div class="bg-light p-5 mb-4 position-relative">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h1 class="display-4">Produtos</h1>
          <p class="lead">Veja todos os produtos disponíveis!</p>
          <nav>
            <ul class="nav mt-4">
              <li class="nav-item">
                <a class="nav-link active fw-bold" href="/trabalho-facul-catalogo/app/auth/home.php">Início</a>
              </li>
              <li>
                <a class="nav-link active text-primary fw-bold"
                  href="/trabalho-facul-catalogo/app/auth/criar-produtos.php">Cadastro de produtos</a>
              </li>
              <li>
                <a class="nav-link active fw-bold"
                  href="/trabalho-facul-catalogo/app/auth/criar-categorias.php">Cadastro de categorias</a>
              </li>
            </ul>
          </nav>
        </div>
        <img src="/trabalho-facul-catalogo/assets/img/Icon.png" alt="Banner" class="banner-img" />
      </div>
      <?php if ($usuario_nome): ?>
        <div class="position-absolute top-0 end-0 mt-3 me-4">
          <span class="badge bg-primary fs-6 p-2">👤 <?php echo htmlspecialchars($usuario_nome); ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- Lista de produtos -->
  <div class="container">
    <h2 class="mb-4">Todos os Produtos</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col">
            <div class="cartao h-100 card">
              <img src="/trabalho-facul-catalogo/assets/img/<?php echo htmlspecialchars($row['imagem']); ?>"
                class="card-img-top" alt="<?php echo htmlspecialchars($row['nome']); ?>" />
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['nome']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($row['descricao']); ?></p>
                <p class="card-text fw-bold">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
                <p class="card-text"><strong>Categoria:</strong> <?php echo htmlspecialchars($row['categoria_nome']); ?></p>
                <p class="card-text"><strong>Adicionado por:</strong>
                  <?php echo htmlspecialchars($row['usuario_nome'] ?? 'Desconhecido'); ?></p>
                <a href="#" class="btn btn-primary">Comprar</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col">
          <div class="alert alert-warning">Nenhum produto cadastrado.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- Rodapé -->
  <footer class="bg-primary text-white text-center py-3 mt-5">
    <div class="container">&copy; 2025 Porter Stranding.</div>
  </footer>
</body>

</html>