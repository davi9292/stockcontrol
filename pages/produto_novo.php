<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_administrador();

$erros = [];
$valores = ['nome' => '', 'descricao' => '', 'quantidade' => '', 'preco' => '', 'status' => 'ativo', 'comentario_inicial' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($valores as $chave => $_) $valores[$chave] = trim((string) ($_POST[$chave] ?? ''));
    $erros = validar_produto($valores, true, $_FILES['imagem'] ?? []);
    if (!$erros) {
        $imagemNome = null;
        try {
            $imagemNome = salvar_imagem($_FILES['imagem']);
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO produtos (nome, descricao, quantidade, preco, status, imagem) VALUES (:nome, :descricao, :quantidade, :preco, :status, :imagem)');
            $stmt->execute([
                'nome' => $valores['nome'], 'descricao' => $valores['descricao'],
                'quantidade' => (int) $valores['quantidade'], 'preco' => number_format((float) str_replace(',', '.', $valores['preco']), 2, '.', ''),
                'status' => $valores['status'], 'imagem' => $imagemNome,
            ]);
            $produtoId = (int) $pdo->lastInsertId();
            if ($valores['comentario_inicial'] !== '') {
                $comentario = $pdo->prepare('INSERT INTO comentarios (produto_id, usuario_id, texto) VALUES (:produto_id, :usuario_id, :texto)');
                $comentario->execute(['produto_id' => $produtoId, 'usuario_id' => usuario_atual()['id'], 'texto' => $valores['comentario_inicial']]);
            }
            $pdo->commit();
            flash('success', 'Produto cadastrado com sucesso.');
            redirect('pages/produtos.php');
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            remover_imagem($imagemNome);
            $erros[] = 'Não foi possível cadastrar o produto. Revise os dados e tente novamente.';
        }
    }
}
$tituloPagina = 'Novo produto';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="form-page-heading"><a class="back-link" href="<?= e(app_url('pages/produtos.php')) ?>">← Voltar para produtos</a><p class="eyebrow mt-3 mb-1">CATÁLOGO</p><h1 class="h2">Novo produto</h1><p class="text-secondary">Preencha os dados para adicionar um item ao estoque.</p></div>
<?php if ($erros): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form id="formProduto" class="form-card" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" maxlength="160" required value="<?= e($valores['nome']) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status" required><option value="ativo" <?= $valores['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option><option value="inativo" <?= $valores['status'] === 'inativo' ? 'selected' : '' ?>>Inativo</option></select></div>
        <div class="col-12"><label class="form-label" for="descricao">Descrição</label><textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= e($valores['descricao']) ?></textarea></div>
        <div class="col-md-4"><label class="form-label" for="quantidade">Quantidade</label><input class="form-control" id="quantidade" name="quantidade" type="number" min="0" step="1" required value="<?= e($valores['quantidade']) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="preco">Preço (R$)</label><input class="form-control" id="preco" name="preco" type="number" min="0" step="0.01" required value="<?= e($valores['preco']) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="imagem">Imagem (JPG, PNG ou WEBP; até 2 MB)</label><input class="form-control" id="imagem" name="imagem" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required></div>
        <div class="col-12"><label class="form-label" for="comentario_inicial">Comentário inicial <span class="text-secondary">(opcional)</span></label><textarea class="form-control" id="comentario_inicial" name="comentario_inicial" rows="2"><?= e($valores['comentario_inicial']) ?></textarea></div>
    </div>
    <div class="form-actions"><a class="btn btn-light" href="<?= e(app_url('pages/produtos.php')) ?>">Cancelar</a><button class="btn btn-primary" id="btnSalvarProduto" type="submit" disabled>Cadastrar</button></div>
</form>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
