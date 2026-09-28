<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Produto inválido.'); }
$stmt = $pdo->prepare('SELECT id, nome FROM produtos WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$produto = $stmt->fetch();
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
$erro = '';
$texto = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $texto = trim((string) ($_POST['texto'] ?? ''));
    if ($texto === '') {
        $erro = 'Digite um comentário antes de cadastrar.';
    } else {
        $insert = $pdo->prepare('INSERT INTO comentarios (produto_id, usuario_id, texto) VALUES (:produto_id, :usuario_id, :texto)');
        $insert->execute(['produto_id' => $id, 'usuario_id' => usuario_atual()['id'], 'texto' => $texto]);
        flash('success', 'Sucesso! Comentário cadastrado para o produto.');
        redirect('pages/comentarios.php?id=' . $id);
    }
}
$tituloPagina = 'Adicionar comentário';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="form-page-heading"><a class="back-link" href="<?= e(app_url('pages/comentarios.php?id=' . $id)) ?>">← Voltar para comentários</a><p class="eyebrow mt-3 mb-1">PRODUTO</p><h1 class="h2">Adicionar comentário</h1><p class="text-secondary mb-0"><?= e($produto['nome']) ?></p></div>
<?php if ($erro !== ''): ?><div class="alert alert-danger" role="alert"><?= e($erro) ?></div><?php endif; ?>
<form class="form-card" method="post" id="formComentario" novalidate>
    <?= csrf_field() ?>
    <label class="form-label" for="texto">Comentário</label>
    <textarea class="form-control" id="texto" name="texto" rows="6" required><?= e($texto) ?></textarea>
    <div class="invalid-feedback">Digite um comentário antes de cadastrar.</div>
    <div class="form-actions"><a class="btn btn-light" href="<?= e(app_url('pages/produtos.php')) ?>">Fechar</a><button class="btn btn-primary" type="submit">Cadastrar</button></div>
</form>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
