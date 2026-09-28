<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_login();

$stmt = $pdo->query("SELECT id, nome, descricao, quantidade, preco, status, imagem FROM produtos WHERE status = 'ativo' AND excluido_em IS NULL ORDER BY nome");
$produtos = $stmt->fetchAll();
$tituloPagina = 'Produtos';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div><p class="eyebrow mb-1">ESTOQUE</p><h1 class="h2 mb-0">Produtos</h1></div>
    <?php if (eh_administrador()): ?><a class="btn btn-primary" href="<?= e(app_url('pages/produto_novo.php')) ?>">+ Novo produto</a><?php endif; ?>
</div>
<?php if (!$produtos): ?>
    <div class="empty-state"><h2 class="h5">Nenhum produto ativo</h2><p class="mb-0">Quando houver produtos ativos, eles aparecerão aqui.</p></div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($produtos as $produto): ?>
            <div class="col-sm-6 col-xl-4">
                <article class="card product-card h-100">
                    <?php if ($produto['imagem']): ?>
                        <img class="product-image" src="<?= e(app_url('uploads/' . rawurlencode($produto['imagem']))) ?>" alt="Imagem do produto <?= e($produto['nome']) ?>">
                    <?php else: ?>
                        <div class="product-image product-placeholder" role="img" aria-label="Sem imagem cadastrada">Sem imagem</div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between gap-2 align-items-start"><h2 class="h5 card-title"><?= e($produto['nome']) ?></h2><span class="badge text-bg-success">Ativo</span></div>
                        <p class="card-text text-secondary product-description"><?= nl2br(e($produto['descricao'])) ?></p>
                        <dl class="product-facts mt-auto">
                            <div><dt>Quantidade</dt><dd><?= e($produto['quantidade']) ?> un.</dd></div>
                            <div><dt>Preço</dt><dd>R$ <?= number_format((float) $produto['preco'], 2, ',', '.') ?></dd></div>
                        </dl>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-outline-primary btn-sm" href="<?= e(app_url('pages/comentarios.php?id=' . $produto['id'])) ?>">Comentários</a>
                            <a class="btn btn-outline-secondary btn-sm" href="<?= e(app_url('pages/historico.php?id=' . $produto['id'])) ?>">Histórico</a>
                            <a class="btn btn-outline-secondary btn-sm" href="<?= e(app_url('pages/movimentacao_nova.php?id=' . $produto['id'])) ?>">Movimentar</a>
                            <?php if (eh_administrador()): ?>
                                <a class="btn btn-outline-secondary btn-sm" href="<?= e(app_url('pages/produto_editar.php?id=' . $produto['id'])) ?>">Editar</a>
                                <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalExcluir<?= e($produto['id']) ?>">Excluir</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </div>
            <?php if (eh_administrador()): ?>
                <div class="modal fade" id="modalExcluir<?= e($produto['id']) ?>" tabindex="-1" aria-labelledby="tituloExcluir<?= e($produto['id']) ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                        <div class="modal-header"><h2 class="modal-title fs-5" id="tituloExcluir<?= e($produto['id']) ?>">Excluir produto</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
                        <div class="modal-body">Atenção! Tem certeza que deseja excluir o produto? Essa ação não poderá ser desfeita.</div>
                        <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancelar</button><form method="post" action="<?= e(app_url('acoes/produto_excluir.php')) ?>"><?= csrf_field() ?><input type="hidden" name="produto_id" value="<?= e($produto['id']) ?>"><button class="btn btn-danger" type="submit">Excluir</button></form></div>
                    </div></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
