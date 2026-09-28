# StockControl

Sistema web para controle de estoque. Permite autenticar usuários, consultar produtos ativos, administrar produtos, registrar comentários e controlar entradas e saídas com histórico do responsável, data e observação.

## Tecnologias

- PHP 8+ com PDO e sessões
- MySQL/MariaDB
- HTML5, CSS3, JavaScript puro e Bootstrap 5.3.3
- Mermaid e PlantUML para diagramas

## Instalação

Veja o [guia de instalação e checklist manual](docs/GUIA_INSTALACAO.md). Em resumo: coloque o projeto em `C:\xampp\htdocs\stockcontrol`, inicie Apache e MySQL, importe `script.sql` pelo phpMyAdmin, revise `config/database.php` e abra `http://localhost/stockcontrol`.

### Contas de demonstração

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | `admin@stockcontrol.local` | `password` |
| Usuário | `usuario@stockcontrol.local` | `password` |

## Funcionalidades

- Login com hashes de senha, sessão e perfis administrador/usuário.
- Cadastro, edição, desativação lógica e listagem de produtos com upload validado.
- Comentários vinculados ao produto e ao usuário, em ordem cronológica decrescente.
- Movimentações de entrada/saída com transação e bloqueio de saldo insuficiente.
- Histórico por produto, incluindo ajustes de saldo realizados na edição.
- Consultas preparadas, escape de saída, tokens CSRF e verificação de perfil no servidor.

## Estrutura

```text
acoes/       Operações protegidas por POST
assets/      CSS, JavaScript, logo e favicon
config/      Conexão PDO e inicialização comum
docs/        Diagramas, identidade visual, wireframe e guia de instalação
includes/    Autenticação, helpers e layout
pages/       Telas autenticadas
uploads/     Imagens dos produtos enviadas localmente
script.sql   Banco e dados de demonstração
```

## Wireframe e diagramas

- Especificação detalhada das telas para Figma: [docs/figma-wireframe.md](docs/figma-wireframe.md)
- Arquivo editável no Figma: [StockControl — Wireframes](https://www.figma.com/design/MgYYgGuXfbxIctQq5RGFBt/StockControl-%E2%80%94-Wireframes?node-id=1-188)
- Cópia vetorial local com os cinco frames: [docs/figma/stockcontrol-wireframes.svg](docs/figma/stockcontrol-wireframes.svg)
- MER: [docs/DER.md](docs/DER.md)
- Atividade: [docs/atividade.mmd](docs/atividade.mmd)
- Casos de uso: [docs/casos-de-uso.puml](docs/casos-de-uso.puml)
- Identidade visual: [docs/identidade-visual.md](docs/identidade-visual.md)

## Capturas de tela sugeridas

1. Tela de login.
2. Listagem de produtos no perfil administrador e usuário.
3. Modal/listagem de comentários e formulário de comentário.
4. Cadastro e edição de produto com validação de imagem.
5. Formulário de movimentação e histórico detalhado do produto.

Salve as capturas em `docs/imagens/` antes de incluí-las no README do GitHub.

## Sequência de commits sugerida

1. `chore: initialize StockControl project structure`
2. `feat: add database schema and demo records`
3. `feat: implement authentication and role-based access`
4. `feat: manage products and secure image uploads`
5. `feat: add comments and stock movement history`
6. `docs: add diagrams wireframes and installation guide`
