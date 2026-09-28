<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Produto inválido.'); }
$produtoStmt = $pdo->prepare('SELECT id, nome, status, excluido_em FROM produtos WHERE id = :id LIMIT 1');
$produtoStmt->execute(['id' => $id]);
$produto = $produtoStmt->fetch();
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
$comentarioStmt = $pdo->prepare('SELECT c.texto, c.data_hora, u.nome, u.perfil FROM comentarios c INNER JOIN usuarios u ON u.id = c.usuario_id WHERE c.produto_id = :produto_id ORDER BY c.data_hora DESC, c.id DESC');
$comentarioStmt->execute(['produto_id' => $id]);
$comentarios = $comentarioStmt->fetchAll();
$tituloPagina = 'Comentários';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="form-page-heading"><a class="back-link" href="<?= e(app_url('pages/produtos.php')) ?>">← Voltar para produtos</a><p class="eyebrow mt-3 mb-1">HISTÓRICO DO PRODUTO</p><h1 class="h2">Comentários</h1><p class="text-secondary mb-0"><?= e($produto['nome']) ?></p></div>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-primary" href="<?= e(app_url('pages/comentario_novo.php?id=' . $id)) ?>">Adicionar comentário</a></div>
<?php if (!$comentarios): ?>
    <div class="empty-state"><p class="mb-0">Este produto ainda não tem comentários.</p></div>
<?php else: ?>
    <section class="comment-list" aria-label="Comentários do produto">
        <?php foreach ($comentarios as $comentario): ?>
            <article class="comment-card"><div class="d-flex flex-wrap justify-content-between gap-2 mb-2"><strong><?= e($comentario['nome']) ?> <span class="badge <?= $comentario['perfil'] === 'administrador' ? 'text-bg-primary' : 'text-bg-secondary' ?>"><?= e(ucfirst($comentario['perfil'])) ?></span></strong><time class="text-secondary" datetime="<?= e(date(DATE_ATOM, strtotime($comentario['data_hora']))) ?>"><?= e(date('d/m/Y H:i', strtotime($comentario['data_hora']))) ?></time></div><p class="mb-0 comment-text"><?= nl2br(e($comentario['texto'])) ?></p></article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<div class="form-actions"><a class="btn btn-light" href="<?= e(app_url('pages/produtos.php')) ?>">Fechar</a><a class="btn btn-primary" href="<?= e(app_url('pages/comentario_novo.php?id=' . $id)) ?>">Adicionar comentário</a></div>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
