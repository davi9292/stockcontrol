document.addEventListener('DOMContentLoaded', () => {
  const produtoForm = document.querySelector('#formProduto');
  if (produtoForm) {
    const botao = produtoForm.querySelector('#btnSalvarProduto');
    const campos = [...produtoForm.querySelectorAll('[required]')];
    const atualizarEstado = () => {
      const valido = campos.every((campo) => campo.type === 'file'
        ? campo.files.length > 0 || campo.dataset.opcional === 'true'
        : campo.checkValidity() && campo.value.trim() !== '');
      botao.disabled = !valido;
    };
    campos.forEach((campo) => campo.addEventListener('input', atualizarEstado));
    campos.forEach((campo) => campo.addEventListener('change', atualizarEstado));
    produtoForm.addEventListener('submit', (evento) => {
      atualizarEstado();
      if (!produtoForm.checkValidity() || botao.disabled) {
        evento.preventDefault();
        produtoForm.classList.add('was-validated');
      }
    });
    atualizarEstado();
  }

  const comentarioForm = document.querySelector('#formComentario');
  if (comentarioForm) {
    const campo = comentarioForm.querySelector('#texto');
    comentarioForm.addEventListener('submit', (evento) => {
      if (campo.value.trim() === '') {
        evento.preventDefault();
        campo.classList.add('is-invalid');
        campo.focus();
      }
    });
    campo.addEventListener('input', () => {
      if (campo.value.trim() !== '') campo.classList.remove('is-invalid');
    });
  }
});
