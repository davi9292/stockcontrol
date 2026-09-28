# Guia de instalação e teste — StockControl

## Requisitos

- XAMPP com Apache, MySQL ou MariaDB e PHP 8.0+.
- Extensões PHP `pdo_mysql`, `fileinfo`, `mbstring` e suporte a uploads ativados.
- Conexão com a internet para carregar Bootstrap 5.3.3 pelo CDN usado nas páginas.

## Instalação local

1. Copie/clonar este repositório para `C:\xampp\htdocs\stockcontrol`.
2. Abra o painel do XAMPP e inicie **Apache** e **MySQL**.
3. Acesse `http://localhost/phpmyadmin`.
4. Use **Importar**, selecione `C:\xampp\htdocs\stockcontrol\script.sql` e execute o arquivo. O script cria o banco `stockcontrol`, as tabelas e os registros de exemplo.
5. Confira `config/database.php`. Para a instalação padrão do XAMPP, host `127.0.0.1`, banco `stockcontrol`, usuário `root` e senha vazia. Ajuste se seu MySQL usar outras credenciais.
6. Garanta que `C:\xampp\htdocs\stockcontrol\uploads` permita gravação pelo usuário do Apache. A pasta já inclui regras Apache para negar execução de scripts.
7. Abra `http://localhost/stockcontrol`.

## Credenciais de demonstração

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | `admin@stockcontrol.local` | `password` |
| Usuário | `usuario@stockcontrol.local` | `password` |

As senhas são hashes bcrypt no script e são verificadas com `password_verify()`.

## Checklist manual de testes

### Login e autorização

- [ ] Entrar com cada credencial válida e confirmar que a sessão abre a listagem.
- [ ] Informar senha incorreta e confirmar mensagem “E-mail ou senha inválidos.”.
- [ ] Sair e tentar abrir diretamente `pages/produtos.php`; deve voltar ao login.
- [ ] Como usuário comum, abrir `pages/produto_novo.php`, `pages/produto_editar.php` e enviar `acoes/produto_excluir.php`; o servidor deve negar.
- [ ] Confirmar que “Novo produto”, “Editar” e “Excluir” não aparecem para usuário comum.
- [ ] Sair por GET ou enviar formulário sem token CSRF; a operação deve ser recusada.

### Produtos e imagens

- [ ] Confirmar que a listagem mostra apenas produtos ativos e não excluídos.
- [ ] Como administrador, tentar cadastrar com campo obrigatório vazio; o botão deve permanecer desabilitado e o servidor também deve recusar envio manual.
- [ ] Cadastrar produto com imagem JPG/PNG/WEBP válida de até 2 MB; conferir gravação e redirecionamento à lista.
- [ ] Tentar imagem acima de 2 MB, extensão não permitida, MIME divergente e arquivo renomeado; conferir recusa.
- [ ] Editar produto e confirmar atualização dos dados e registro de movimentação quando o saldo for alterado.
- [ ] Usar “Excluir”, cancelar no modal e então confirmar a exclusão; conferir que o produto deixa a lista e comentários/movimentações continuam no banco.

### Comentários

- [ ] Abrir comentários e conferir perfil, data/hora e ordem do mais recente para o mais antigo.
- [ ] Tentar enviar comentário vazio ou só com espaços; conferir validação no navegador e no servidor.
- [ ] Cadastrar comentário como usuário comum e administrador; confirmar autor e data/hora atuais e a mensagem exata “Sucesso! Comentário cadastrado para o produto.”.

### Movimentações e histórico

- [ ] Registrar entrada e conferir aumento do saldo e registro de responsável/data/hora/observação.
- [ ] Registrar saída menor que o saldo e conferir redução correta.
- [ ] Tentar saída maior que o saldo; conferir recusa e saldo inalterado.
- [ ] Tentar quantidade zero, negativa, fracionada ou produto inativo; conferir recusa.
- [ ] Abrir histórico do produto e conferir entradas/saídas, ajustes feitos pela edição, data, responsável e observação.

### Interface

- [ ] Redimensionar para desktop, tablet e celular e verificar cards, formulários, navbar e modal.
- [ ] Confirmar logo com texto alternativo, favicon, foco de teclado e estados de erro/desabilitado.

