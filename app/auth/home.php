<?php
// Inicia a sessão para manter o login do usuário
session_start();
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Porter Stranding</title>
  <!-- Importa o CSS personalizado -->
  <link rel="stylesheet" href="/trabalho-facul-catalogo/assets/css/style.css" />
  <!-- Importa o Bootstrap para estilos prontos e responsividade -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/trabalho-facul-catalogo/assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/trabalho-facul-catalogo/assets/js/script.js"></script>
</head>

<body>
  <!-- Banner de boas-vindas com itens do menu -->
  <div class="bg-light p-5 mb-4 position-relative">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
          <h1 class="display-4">Bem-vindo à Porter Stranding!</h1>
          <p class="lead">Nunca longe de você.</p>
          <!-- Itens do menu dentro do banner -->
          <nav>
            <ul class="nav mt-4">
              <li class="nav-item">
                <a class="nav-link active text-primary fw-bold" href="#">Início</a>
              </li>
              <li>
                <a class="nav-link active text-primary fw-bold"
                  href="/trabalho-facul-catalogo/app/auth/produtos.php">Produtos</a>
              </li>
              <li>
                <a class="nav-link active text-primary fw-bold"
                  href="/trabalho-facul-catalogo/app/auth/criar-produtos.php">Cadastro de produtos</a>
              </li>
              <li>
                <a class="nav-link active text-primary fw-bold"
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
  <!-- Seção de produtos em destaque -->
  <div class="container">
    <h2 class="mb-4">Produtos em Destaque</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <!-- Produto 1 -->
      <div class="col">
        <div class="cartao h-100 card">
          <!-- Imagem do produto -->
          <img src="/trabalho-facul-catalogo/assets/img/Nintendo Switch 2.png" class="card-img-top" alt="Produto 1" />
          <div class="card-body">
            <h5 class="card-title">Nintendo Switch 2</h5>
            <p class="card-text">
              Compre já o console mais inovador da atualidade.
            </p>
            <p class="card-text fw-bold">R$ 2.999,90</p>
            <a href="#" class="btn btn-primary">Comprar</a>
          </div>
        </div>
      </div>
      <!-- Produto 2 -->
      <div class="col">
        <div class="cartao h-100 card">
          <!-- Imagem do produto -->
          <img src="/trabalho-facul-catalogo/assets/img/Death Stranding 2.png" class="card-img-top" alt="Produto 2" />
          <div class="card-body">
            <h5 class="card-title">Death stranding 2 - PS5</h5>
            <p class="card-text">
              Tenha a maior experiência de sua vida com a nova arte de Hideo
              Kojima.
            </p>
            <p class="card-text fw-bold">R$ 349,90</p>
            <a href="#" class="btn btn-primary">Comprar</a>
          </div>
        </div>
      </div>
      <!-- Produto 3 -->
      <div class="col">
        <div class="cartao h-100 card">
          <!-- Imagem do produto -->
          <img src="/trabalho-facul-catalogo/assets/img/rtx 5090.png" class="card-img-top" alt="Produto 3" />
          <div class="card-body">
            <h5 class="card-title">Gigabyte Windforce RTX 5090</h5>
            <p class="card-text">
              A placa de viodeo mais poderosa da atualidade.
            </p>
            <p class="card-text fw-bold">R$ 99.999,90</p>
            <a href="#" class="btn btn-primary">Comprar</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Rodapé do site -->
  <footer class="bg-primary text-white text-center py-3 mt-5">
    <div class="container">&copy; 2025 Porter Stranding.</div>
  </footer>
</body>

</html>