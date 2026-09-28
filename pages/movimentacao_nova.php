<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_login();
$idInicial = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$produtos = $pdo->query("SELECT id, nome, quantidade FROM produtos WHERE status = 'ativo' AND excluido_em IS NULL ORDER BY nome")->fetchAll();
$erros = [];
$valores = ['produto_id' => (string) $idInicial, 'tipo' => '', 'quantidade' => '', 'observacao' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($valores as $chave => $_) $valores[$chave] = trim((string) ($_POST[$chave] ?? ''));
    $produtoId = filter_var($valores['produto_id'], FILTER_VALIDATE_INT);
    $quantidade = filter_var($valores['quantidade'], FILTER_VALIDATE_INT);
    if (!$produtoId) $erros[] = 'Selecione um produto válido.';
    if (!in_array($valores['tipo'], ['entrada', 'saida'], true)) $erros[] = 'Selecione entrada ou saída.';
    if ($quantidade === false || $quantidade < 1) $erros[] = 'A quantidade da movimentação deve ser maior que zero.';
    if (!$erros) {
        try {
            $pdo->beginTransaction();
            $saldoStmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = :id AND status = 'ativo' AND excluido_em IS NULL FOR UPDATE");
            $saldoStmt->execute(['id' => $produtoId]);
            $produto = $saldoStmt->fetch();
            if (!$produto) throw new DomainException('O produto selecionado não está ativo.');
            $saldoAtual = (int) $produto['quantidade'];
            if ($valores['tipo'] === 'saida' && $quantidade > $saldoAtual) throw new DomainException('A saída não pode ser maior que o saldo disponível (' . $saldoAtual . ').');
            $novoSaldo = $valores['tipo'] === 'entrada' ? $saldoAtual + $quantidade : $saldoAtual - $quantidade;
            $atualizar = $pdo->prepare('UPDATE produtos SET quantidade = :quantidade WHERE id = :id');
            $atualizar->execute(['quantidade' => $novoSaldo, 'id' => $produtoId]);
            $movimento = $pdo->prepare('INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, observacao) VALUES (:produto_id, :usuario_id, :tipo, :quantidade, :observacao)');
            $movimento->execute(['produto_id' => $produtoId, 'usuario_id' => usuario_atual()['id'], 'tipo' => $valores['tipo'], 'quantidade' => $quantidade, 'observacao' => $valores['observacao'] !== '' ? $valores['observacao'] : null]);
            $pdo->commit();
            flash('success', 'Movimentação registrada e estoque atualizado.');
            redirect('pages/produtos.php');
        } catch (DomainException $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $erros[] = $exception->getMessage();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $erros[] = 'Não foi possível registrar a movimentação. Tente novamente.';
        }
    }
}
$tituloPagina = 'Movimentar estoque';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="form-page-heading"><a class="back-link" href="<?= e(app_url('pages/produtos.php')) ?>">← Voltar para produtos</a><p class="eyebrow mt-3 mb-1">ESTOQUE</p><h1 class="h2">Registrar movimentação</h1></div>
<?php if ($erros): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form class="form-card" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label" for="produto_id">Produto</label><select class="form-select" id="produto_id" name="produto_id" required><option value="">Selecione</option><?php foreach ($produtos as $produto): ?><option value="<?= e($produto['id']) ?>" <?= (string) $produto['id'] === $valores['produto_id'] ? 'selected' : '' ?>><?= e($produto['nome']) ?> — saldo: <?= e($produto['quantidade']) ?></option><?php endforeach; ?></select></div>
    <div class="row g-3"><div class="col-md-6"><label class="form-label" for="tipo">Tipo</label><select class="form-select" id="tipo" name="tipo" required><option value="">Selecione</option><option value="entrada" <?= $valores['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option><option value="saida" <?= $valores['tipo'] === 'saida' ? 'selected' : '' ?>>Saída</option></select></div><div class="col-md-6"><label class="form-label" for="quantidade">Quantidade</label><input class="form-control" type="number" min="1" step="1" id="quantidade" name="quantidade" required value="<?= e($valores['quantidade']) ?>"></div></div>
    <div class="mt-3"><label class="form-label" for="observacao">Observação</label><textarea class="form-control" id="observacao" name="observacao" rows="3"><?= e($valores['observacao']) ?></textarea></div>
    <div class="form-actions"><a class="btn btn-light" href="<?= e(app_url('pages/produtos.php')) ?>">Cancelar</a><button class="btn btn-primary" type="submit">Registrar movimentação</button></div>
</form>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
