<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Produto inválido.'); }
$produtoStmt = $pdo->prepare('SELECT id, nome, quantidade FROM produtos WHERE id = :id LIMIT 1');
$produtoStmt->execute(['id' => $id]);
$produto = $produtoStmt->fetch();
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
$stmt = $pdo->prepare('SELECT m.tipo, m.quantidade, m.data_hora, m.observacao, u.nome, u.perfil FROM movimentacoes m INNER JOIN usuarios u ON u.id = m.usuario_id WHERE m.produto_id = :produto_id ORDER BY m.data_hora DESC, m.id DESC');
$stmt->execute(['produto_id' => $id]);
$movimentacoes = $stmt->fetchAll();
$tituloPagina = 'Histórico de movimentações';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="form-page-heading"><a class="back-link" href="<?= e(app_url('pages/produtos.php')) ?>">← Voltar para produtos</a><p class="eyebrow mt-3 mb-1">AUDITORIA DE ESTOQUE</p><h1 class="h2">Histórico de movimentações</h1><p class="text-secondary mb-0"><?= e($produto['nome']) ?> · saldo atual: <?= e($produto['quantidade']) ?> un.</p></div>
<?php if (!$movimentacoes): ?>
    <div class="empty-state"><p class="mb-0">Ainda não há movimentações registradas para este produto.</p></div>
<?php else: ?>
    <div class="table-responsive history-table-wrap"><table class="table align-middle mb-0"><thead><tr><th>Data e hora</th><th>Tipo</th><th>Quantidade</th><th>Responsável</th><th>Observação</th></tr></thead><tbody>
        <?php foreach ($movimentacoes as $movimento): ?>
            <tr><td><time datetime="<?= e(date(DATE_ATOM, strtotime($movimento['data_hora']))) ?>"><?= e(date('d/m/Y H:i', strtotime($movimento['data_hora']))) ?></time></td><td><span class="badge <?= $movimento['tipo'] === 'entrada' ? 'text-bg-success' : 'text-bg-warning' ?>"><?= e(ucfirst($movimento['tipo'])) ?></span></td><td><?= e($movimento['quantidade']) ?> un.</td><td><?= e($movimento['nome']) ?> <span class="text-secondary small">(<?= e($movimento['perfil']) ?>)</span></td><td><?= $movimento['observacao'] !== null && $movimento['observacao'] !== '' ? nl2br(e($movimento['observacao'])) : '<span class="text-secondary">—</span>' ?></td></tr>
        <?php endforeach; ?>
    </tbody></table></div>
<?php endif; ?>
<div class="form-actions"><a class="btn btn-light" href="<?= e(app_url('pages/produtos.php')) ?>">Fechar</a><a class="btn btn-primary" href="<?= e(app_url('pages/movimentacao_nova.php?id=' . $id)) ?>">Registrar movimentação</a></div>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
