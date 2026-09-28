# Wireframe de alta fidelidade — StockControl

Especificação para montar no Figma. Crie uma página chamada **StockControl · Wireframes** e os cinco frames indicados abaixo. Use Auto Layout nos conteúdos empilhados e mantenha os componentes de formulário e botão reutilizáveis. A fonte na aplicação é `Inter, Arial, sans-serif`; a paleta e a logo ficam em `assets/img/` e em `docs/identidade-visual.md`.

## Tokens do arquivo

| Token | Valor |
|---|---|
| Cor/Primária | `#145A8D` |
| Cor/Secundária | `#18A6A6` |
| Cor/Fundo | `#F3F6F8` |
| Cor/Texto | `#20313F` |
| Cor/Superfície | `#FFFFFF` |
| Cor/Borda | `#DCE4E9` |
| Feedback/Sucesso | `#198754` |
| Feedback/Erro | `#DC3545` |
| Feedback/Alerta | `#F0AD4E` |
| Raio pequeno / médio / grande | 6 / 10 / 14 px |
| Espaçamento | 4 / 8 / 12 / 16 / 24 / 32 / 48 px |

Tipografia: títulos de página 32 px/40 px, peso 700; títulos de card 20 px/28 px, peso 600; corpo 16 px/24 px, peso 400; rótulos e metadados 14 px/20 px; botões 14 px/20 px, peso 600. Tamanho mínimo de alvo interativo: 40×40 px.

## 1. Login — `Login / Desktop` (1440×900)

- Fundo `#F3F6F8`; painel branco de 460×520 px, centralizado nos dois eixos, raio 14 px e sombra leve. Em telas estreitas, largura `calc(100% - 32px)`.
- Logo StockControl com 260×64 px no topo do painel, alinhada à esquerda. Título “Acesse sua conta” abaixo, seguido do apoio “Entre para consultar o estoque.”
- Campos “E-mail” e “Senha”, largura total, altura 48 px, borda `#DCE4E9`, raio 6 px, distância vertical 20 px. Senha mascarada.
- Botão primário “Entrar”, largura total e altura 48 px, fundo `#145A8D`, texto branco.
- Estado de erro: alerta `#DC3545` em fundo `#FCEBEC`, acima dos campos, com o texto de erro do formulário.
- Hover: botão `#104A74`; foco: anel de 3 px `#18A6A6` com opacidade de 20%; campos inválidos: borda e mensagem `#DC3545`.

## 2. Listagem — `Produtos / Desktop` (1440×1024)

- Fundo `#F3F6F8`; navbar de ponta a ponta, altura 76 px, fundo `#145A8D`. Logo em fundo branco de 205×51 px à esquerda; “Novo produto” somente para administrador; nome do usuário e botão “Sair” à direita.
- Área central de até 1200 px, margem superior 40 px. Título “Produtos”, 32 px, e botão “+ Novo produto” no mesmo alinhamento à direita para administrador.
- Grid de três colunas com espaçamento de 24 px. Cards brancos, raio 14 px, borda `#DCE4E9`, sombra leve, largura aproximada 384 px.
- Cada card: imagem 100%×190 px com `object-fit: cover`; nome e selo “Ativo”; descrição; linhas de quantidade e preço; ações “Comentários”, “Histórico” e “Movimentar”. Administrador também vê “Editar” e “Excluir”. Em tablet, duas colunas; em celular, uma coluna.
- Modal de confirmação central, largura 520 px, overlay preto a 45%, título “Excluir produto”, texto exato **“Atenção! Tem certeza que deseja excluir o produto? Essa ação não poderá ser desfeita.”**, ações “Cancelar” e “Excluir”.
- Hover do card: sombra um pouco mais forte; links e botões seguem os estados de foco/hover dos tokens. Estoque zero continua visível como `0 un.`; produto inativo ou excluído não aparece na listagem.

## 3. Comentários — `Comentários / Modal` (600×680, sobre Produtos)

- Criar o modal sobre uma cópia do frame de listagem escurecida com overlay preto a 45%. Modal branco de 600×680 px, centralizado, raio 14 px.
- Cabeçalho com “Comentários”, nome do produto em texto secundário e botão fechar de 40×40 px.
- Corpo rolável com comentários do mais recente para o mais antigo. Cada item contém nome, selo do perfil (`#145A8D` para administrador e cinza para usuário), data/hora e texto. Separadores `#DCE4E9` e espaçamento de 16 px.
- Rodapé fixo com botão secundário “Fechar” e primário “Adicionar comentário”. Em celular, modal ocupa a largura disponível, respeitando 16 px de margem.
- Estado vazio: “Este produto ainda não tem comentários.” Estado de carregamento: três linhas cinza claras. Texto deve quebrar linha sem ultrapassar o modal.

## 4. Cadastro de comentário — `Novo comentário / Desktop` (1440×900)

- Usa navbar autenticada e fundo `#F3F6F8`. Conteúdo centrado, largura máxima 850 px; link “← Voltar para comentários”, título “Adicionar comentário” e nome do produto.
- Card branco com raio 14 px e padding 32 px. Campo “Comentário” em textarea de 6 linhas, largura total, borda `#DCE4E9`, raio 6 px.
- Rodapé alinhado à direita com “Fechar” (retorna aos produtos) e “Cadastrar” (primário). Botões altura 44 px.
- Estado de texto vazio: textarea borda `#DC3545` e mensagem “Digite um comentário antes de cadastrar.”; estado enviado mostra alerta de sucesso com o texto exato “Sucesso! Comentário cadastrado para o produto.”

## 5. Cadastro de produto — `Novo produto / Desktop` (1440×1100)

- Navbar autenticada; conteúdo centrado com largura máxima 850 px. Link “← Voltar para produtos”, título “Novo produto” e texto de apoio.
- Card branco com raio 14 px e padding 32 px. Grid de duas colunas: Nome ocupa 8/12, Status 4/12; Descrição ocupa a linha toda; Quantidade, Preço e Imagem dividem a linha em três colunas. Comentário inicial opcional ocupa a linha toda.
- Inputs e selects com altura 48 px; textarea descrição 112 px e comentário inicial 72 px. Upload mostra ajuda “JPG, PNG ou WEBP; até 2 MB”.
- Rodapé à direita: “Cancelar” e “Cadastrar”. “Cadastrar” começa desabilitado em cinza até que todos os campos obrigatórios, incluindo imagem, estejam válidos; depois usa `#145A8D`.
- Estado de erro: mensagem geral em alerta `#DC3545`; campo inválido com borda vermelha e texto abaixo. Em viewport menor que 768 px, campos passam a uma coluna.

## Componentes e estados a criar no Figma

- Botão: `Primário`, `Secundário`, `Perigo`, cada um em estados `Padrão`, `Hover`, `Foco` e `Desabilitado`.
- Campo: `Texto`, `Senha`, `Select`, `Textarea` e `Upload`, cada um em estados `Padrão`, `Foco` e `Erro`.
- Card de produto, selo de perfil/status, alerta, comentário, linha de histórico e modal de confirmação.
- Use a logo existente como SVG importado e conserve o texto alternativo no protótipo/documentação: `StockControl — controle de estoque`.

