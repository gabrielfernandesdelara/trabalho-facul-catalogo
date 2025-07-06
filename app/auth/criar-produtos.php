<?php
// Inicia a sessão para manter o login do usuário
session_start();
require_once __DIR__ . '/../../banco/connect.php';
require_once __DIR__ . '/../../banco/verificar-login.php';
// Pega o nome do usuário logado, se houver
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
$conn = conectarBanco();
$mensagem = "";
// CRIAR produto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['novo_produto'])) {
  // Pega os dados do formulário e remove espaços extras
  $nome = trim($_POST['nome'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');
  $preco = floatval($_POST['preco'] ?? 0);
  $estoque = intval($_POST['estoque'] ?? 0);
  $categoria_id = intval($_POST['categoria_id'] ?? 0);
  // Upload da imagem
  $imagem_nome = "";
  if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $imagem_nome = uniqid() . '.' . $ext;
    $destino = __DIR__ . '/../../assets/img/' . $imagem_nome;
    move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
  }
  // Verifica se todos os campos foram preenchidos
  if ($nome && $preco && $estoque && $categoria_id && $imagem_nome) {
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    // Insere o novo produto no banco de dados, incluindo o usuário que cadastrou
    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisii", $nome, $descricao, $preco, $estoque, $imagem_nome, $categoria_id, $usuario_id);
    if ($stmt->execute()) {
      $mensagem = '<div class="alert alert-success">Produto criado!</div>';
    } else {
      $mensagem = '<div class="alert alert-danger">Erro ao criar produto.</div>';
    }
    $stmt->close();
  } else {
    // Se algum campo não foi preenchido, mostra aviso
    $mensagem = '<div class="alert alert-warning">Preencha todos os campos e envie uma imagem.</div>';
  }
}
// EDITAR produto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editar_produto'])) {
  $id = intval($_POST['id']);
  $nome = trim($_POST['nome'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');
  $preco = floatval($_POST['preco'] ?? 0);
  $estoque = intval($_POST['estoque'] ?? 0);
  $categoria_id = intval($_POST['categoria_id'] ?? 0);
  // Atualiza imagem se enviada
  $imagem_nome = $_POST['imagem_atual'] ?? '';
  if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $imagem_nome = uniqid() . '.' . $ext;
    $destino = __DIR__ . '/../../assets/img/' . $imagem_nome;
    move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
  }
  // Verifica se todos os campos foram preenchidos
  if ($nome && $preco && $estoque && $categoria_id) {
    $stmt = $conn->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, imagem=?, categoria_id=? WHERE id=?");
    $stmt->bind_param("ssdisii", $nome, $descricao, $preco, $estoque, $imagem_nome, $categoria_id, $id);
    if ($stmt->execute()) {
      $mensagem = '<div class="alert alert-success">Produto editado!</div>';
    } else {
      $mensagem = '<div class="alert alert-danger">Erro ao editar produto.</div>';
    }
    $stmt->close();
  } else {
    // Se algum campo não foi preenchido, mostra aviso
    $mensagem = '<div class="alert alert-warning">Preencha todos os campos.</div>';
  }
}
// EXCLUIR produto
if (isset($_GET['excluir'])) {
  $id = intval($_GET['excluir']);
  $stmt = $conn->prepare("DELETE FROM produtos WHERE id=?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    $mensagem = '<div class="alert alert-success">Produto excluído!</div>';
  } else {
    $mensagem = '<div class="alert alert-danger">Erro ao excluir produto.</div>';
  }
  $stmt->close();
}
// BUSCAR produtos (agora trazendo o nome do usuário)
$produtos = [];
$sql = "SELECT p.*, c.nome AS categoria_nome, u.nome AS usuario_nome
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
  }
}
// BUSCAR categorias para o select
$categorias = [];
$res = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $categorias[] = $row;
  }
}
// Se for editar, busca o produto
$produtoEditar = null;
if (isset($_GET['editar'])) {
  $id = intval($_GET['editar']);
  $stmt = $conn->prepare("SELECT * FROM produtos WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $produtoEditar = $res->fetch_assoc();
  $stmt->close();
}
$conn->close();
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
  <!-- Barra superior igual à home -->
  <div class="bg-light p-5 mb-4 position-relative">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h1 class="display-4">Produtos</h1>
          <p class="lead">Gerencie os produtos do catálogo!</p>
          <nav>
            <ul class="nav mt-4">
              <li class="nav-item">
                <a class="nav-link active fw-bold" href="/trabalho-facul-catalogo/app/auth/home.php">Início</a>
              </li>
              <li>
                <a class="nav-link active fw-bold" href="/trabalho-facul-catalogo/app/auth/produtos.php">Produtos</a>
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
        <div class="position-absolute top-0 end-0 mt-3 me-4 d-flex align-items-center gap-2">
          <span class="usuario-nome badge bg-primary fs-6 p-2">👤 <?php echo htmlspecialchars($usuario_nome); ?></span>
          <a href="/trabalho-facul-catalogo/app/public/logout.php" class="logout-link ms-2">Sair</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="container d-flex flex-column align-items-center" style="min-height: 60vh;">
    <?php echo $mensagem; ?>

    <!-- Botão criar produto -->
    <a href="?novo=1" class="btn btn-primary mb-4" style="max-width: 250px;">Criar novo produto</a>

    <!-- Formulário de criar produto -->
    <?php if (isset($_GET['novo'])): ?>
      <div class="card mb-4" style="max-width: 600px; width: 100%;">
        <div class="card-body">
          <h5 class="card-title mb-3">Novo Produto</h5>
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome do produto</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
              <label for="descricao" class="form-label">Descrição</label>
              <textarea class="form-control" id="descricao" name="descricao" required></textarea>
            </div>
            <div class="mb-3">
              <label for="preco" class="form-label">Preço</label>
              <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
            </div>
            <div class="mb-3">
              <label for="estoque" class="form-label">Estoque</label>
              <input type="number" class="form-control" id="estoque" name="estoque" required>
            </div>
            <div class="mb-3">
              <label for="categoria_id" class="form-label">Categoria</label>
              <select class="form-select" id="categoria_id" name="categoria_id" required>
                <option value="">Selecione</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nome']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="imagem" class="form-label">Imagem</label>
              <input type="file" class="form-control" id="imagem" name="imagem" required>
            </div>
            <button type="submit" name="novo_produto" class="btn btn-primary">Criar</button>
            <a href="criar-produtos.php" class="btn btn-cancelar ms-2">Cancelar</a>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- Formulário de editar produto -->
    <?php if ($produtoEditar): ?>
      <div class="card mb-4" style="max-width: 600px; width: 100%;">
        <div class="card-body">
          <h5 class="card-title mb-3">Editar Produto</h5>
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $produtoEditar['id']; ?>">
            <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($produtoEditar['imagem']); ?>">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome do produto</label>
              <input type="text" class="form-control" id="nome" name="nome" required
                value="<?php echo htmlspecialchars($produtoEditar['nome']); ?>">
            </div>
            <div class="mb-3">
              <label for="descricao" class="form-label">Descrição</label>
              <textarea class="form-control" id="descricao" name="descricao"
                required><?php echo htmlspecialchars($produtoEditar['descricao']); ?></textarea>
            </div>
            <div class="mb-3">
              <label for="preco" class="form-label">Preço</label>
              <input type="number" step="0.01" class="form-control" id="preco" name="preco" required
                value="<?php echo htmlspecialchars($produtoEditar['preco']); ?>">
            </div>
            <div class="mb-3">
              <label for="estoque" class="form-label">Estoque</label>
              <input type="number" class="form-control" id="estoque" name="estoque" required
                value="<?php echo htmlspecialchars($produtoEditar['estoque']); ?>">
            </div>
            <div class="mb-3">
              <label for="categoria_id" class="form-label">Categoria</label>
              <select class="form-select" id="categoria_id" name="categoria_id" required>
                <option value="">Selecione</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>" <?php if ($produtoEditar['categoria_id'] == $cat['id'])
                       echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['nome']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="imagem" class="form-label">Imagem</label>
              <input type="file" class="form-control" id="imagem" name="imagem">
              <?php if ($produtoEditar['imagem']): ?>
                <img src="/trabalho-facul-catalogo/assets/img/<?php echo htmlspecialchars($produtoEditar['imagem']); ?>"
                  alt="Imagem atual" style="max-width: 100px; margin-top: 8px;">
              <?php endif; ?>
            </div>
            <button type="submit" name="editar_produto" class="btn btn-primary">Salvar</button>
            <a href="criar-produtos.php" class="btn btn-cancelar ms-2">Cancelar</a>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- Lista de produtos -->
    <div class="row row-cols-1 row-cols-md-3 g-4 w-100">
      <?php if (count($produtos) > 0): ?>
        <?php foreach ($produtos as $prod): ?>
          <div class="col">
            <div class="cartao h-100 card">
              <img src="/trabalho-facul-catalogo/assets/img/<?php echo htmlspecialchars($prod['imagem']); ?>"
                class="card-img-top" alt="<?php echo htmlspecialchars($prod['nome']); ?>" />
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($prod['nome']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($prod['descricao']); ?></p>
                <p class="card-text fw-bold">R$ <?php echo number_format($prod['preco'], 2, ',', '.'); ?></p>
                <p class="card-text"><strong>Estoque:</strong> <?php echo $prod['estoque']; ?></p>
                <p class="card-text"><strong>Categoria:</strong> <?php echo htmlspecialchars($prod['categoria_nome']); ?>
                </p>
                <p class="card-text"><strong>Adicionado por:</strong>
                  <?php echo htmlspecialchars($prod['usuario_nome'] ?? 'Desconhecido'); ?></p>
                <a href="?editar=<?php echo $prod['id']; ?>" class="btn btn-sm btn-primary me-2">Editar</a>
                <a href="?excluir=<?php echo $prod['id']; ?>" class="btn btn-cancelar btn-sm"
                  onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col">
          <div class="alert alert-warning text-center">Nenhum produto cadastrado.</div>
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