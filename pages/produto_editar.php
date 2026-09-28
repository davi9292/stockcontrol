<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_administrador();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Produto inválido.'); }
$consulta = $pdo->prepare('SELECT * FROM produtos WHERE id = :id AND excluido_em IS NULL LIMIT 1');
$consulta->execute(['id' => $id]);
$produto = $consulta->fetch();
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
$valores = ['nome' => $produto['nome'], 'descricao' => $produto['descricao'], 'quantidade' => (string) $produto['quantidade'], 'preco' => (string) $produto['preco'], 'status' => $produto['status']];
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($valores as $chave => $_) $valores[$chave] = trim((string) ($_POST[$chave] ?? ''));
    $erros = validar_produto($valores, true, $_FILES['imagem'] ?? []);
    if ($produto['excluido_em'] !== null) $erros[] = 'Um produto excluído não pode ser reativado.';
    $imagemNova = isset($_FILES['imagem']['error']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE;
    $nomeImagem = $produto['imagem'];
    if (!$erros) {
        try {
            if ($imagemNova) $nomeImagem = salvar_imagem($_FILES['imagem']);
            $pdo->beginTransaction();
            $saldoStmt = $pdo->prepare('SELECT quantidade FROM produtos WHERE id = :id AND excluido_em IS NULL FOR UPDATE');
            $saldoStmt->execute(['id' => $id]);
            $saldoAtual = $saldoStmt->fetchColumn();
            if ($saldoAtual === false) throw new RuntimeException('O produto não está mais disponível para edição.');
            $saldoAtual = (int) $saldoAtual;
            $saldoNovo = (int) $valores['quantidade'];
            $stmt = $pdo->prepare('UPDATE produtos SET nome = :nome, descricao = :descricao, quantidade = :quantidade, preco = :preco, status = :status, imagem = :imagem WHERE id = :id AND excluido_em IS NULL');
            $stmt->execute([
                'nome' => $valores['nome'], 'descricao' => $valores['descricao'], 'quantidade' => (int) $valores['quantidade'],
                'preco' => number_format((float) str_replace(',', '.', $valores['preco']), 2, '.', ''), 'status' => $valores['status'], 'imagem' => $nomeImagem, 'id' => $id,
            ]);
            if ($saldoNovo !== $saldoAtual) {
                $ajuste = $pdo->prepare('INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, observacao) VALUES (:produto_id, :usuario_id, :tipo, :quantidade, :observacao)');
                $ajuste->execute([
                    'produto_id' => $id, 'usuario_id' => usuario_atual()['id'],
                    'tipo' => $saldoNovo > $saldoAtual ? 'entrada' : 'saida',
                    'quantidade' => abs($saldoNovo - $saldoAtual),
                    'observacao' => 'Ajuste de saldo durante edição de produto.',
                ]);
            }
            $pdo->commit();
            if ($imagemNova && $produto['imagem'] !== $nomeImagem) remover_imagem($produto['imagem']);
            flash('success', 'Produto atualizado com sucesso.');
            redirect('pages/produtos.php');
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($imagemNova && $nomeImagem !== $produto['imagem']) remover_imagem($nomeImagem);
            $erros[] = 'Não foi possível atualizar o produto. Revise os dados e tente novamente.';
        }
    }
}
$tituloPagina = 'Editar produto';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="form-page-heading"><a class="back-link" href="<?= e(app_url('pages/produtos.php')) ?>">← Voltar para produtos</a><p class="eyebrow mt-3 mb-1">CATÁLOGO</p><h1 class="h2">Editar produto</h1></div>
<?php if ($erros): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form id="formProduto" class="form-card" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" maxlength="160" required value="<?= e($valores['nome']) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status" required><option value="ativo" <?= $valores['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option><option value="inativo" <?= $valores['status'] === 'inativo' ? 'selected' : '' ?>>Inativo</option></select></div>
        <div class="col-12"><label class="form-label" for="descricao">Descrição</label><textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= e($valores['descricao']) ?></textarea></div>
        <div class="col-md-4"><label class="form-label" for="quantidade">Quantidade</label><input class="form-control" id="quantidade" name="quantidade" type="number" min="0" step="1" required value="<?= e($valores['quantidade']) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="preco">Preço (R$)</label><input class="form-control" id="preco" name="preco" type="number" min="0" step="0.01" required value="<?= e($valores['preco']) ?>"></div>
        <div class="col-md-4"><label class="form-label" for="imagem">Imagem (JPG, PNG ou WEBP; até 2 MB)</label><input class="form-control" id="imagem" name="imagem" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required><div class="form-text">Envie a imagem atualizada do produto.</div></div>
    </div>
    <div class="form-actions"><a class="btn btn-light" href="<?= e(app_url('pages/produtos.php')) ?>">Cancelar</a><button class="btn btn-primary" id="btnSalvarProduto" type="submit" disabled>Salvar alterações</button></div>
</form>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
