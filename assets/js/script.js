document.addEventListener("DOMContentLoaded", function () {
  // Se a variável produtoEditarAtivo existir e for verdadeira,
  // abre automaticamente o modal de edição de produto quando a página carrega.
  if (typeof produtoEditarAtivo !== "undefined" && produtoEditarAtivo) {
    var modal = new bootstrap.Modal(document.getElementById("modalProduto"));
    modal.show();
  }

  // Procura o botão de "Novo Produto" na página
  var btnNovoProduto = document.getElementById("btnNovoProduto");
  if (btnNovoProduto) {
    // Quando clicar no botão de novo produto:
    btnNovoProduto.onclick = function (e) {
      e.preventDefault(); // Impede o comportamento padrão do link/botão
      // Remove parâmetros da URL (tipo ?editar=) para evitar problemas
      window.history.replaceState({}, document.title, window.location.pathname);
      // Pega o formulário dentro do modal de produto
      var form = document.querySelector("#modalProduto form");
      if (form) {
        form.reset(); // Limpa todos os campos do formulário
        // Limpa também os campos escondidos de edição, se existirem
        var inputId = form.querySelector('input[name="id"]');
        var inputImg = form.querySelector('input[name="imagem_atual"]');
        if (inputId) inputId.value = "";
        if (inputImg) inputImg.value = "";
      }
      // Abre o modal para cadastrar um novo produto
      var modal = new bootstrap.Modal(document.getElementById("modalProduto"));
      modal.show();
    };
  }

  // Para todos os links que têm o atributo data-confirm:
  document.querySelectorAll("a[data-confirm]").forEach(function (link) {
    link.addEventListener("click", function (e) {
      // Mostra uma caixinha de confirmação antes de fazer a ação (tipo excluir)
      if (!confirm(link.getAttribute("data-confirm"))) {
        // Se a pessoa clicar em "Cancelar", não faz nada
        e.preventDefault();
      }
    });
  });
});
