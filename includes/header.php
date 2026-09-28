<?php
declare(strict_types=1);
exigir_login();
$usuarioNav = usuario_atual();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tituloPagina ?? 'StockControl') ?> | StockControl</title>
    <link rel="icon" href="<?= e(app_url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= e(app_url('pages/produtos.php')) ?>"><img src="<?= e(app_url('assets/img/logo.svg')) ?>" alt="StockControl — controle de estoque"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto">
                <?php if (eh_administrador()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(app_url('pages/produto_novo.php')) ?>">Novo produto</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text me-lg-3">Olá, <?= e($usuarioNav['nome']) ?></span>
            <form method="post" action="<?= e(app_url('logout.php')) ?>">
                <?= csrf_field() ?>
                <button class="btn btn-outline-light btn-sm" type="submit">Sair</button>
            </form>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php if ($mensagem = flash('success')): ?><div class="alert alert-success" role="status"><?= e($mensagem) ?></div><?php endif; ?>
    <?php if ($mensagem = flash('error')): ?><div class="alert alert-danger" role="alert"><?= e($mensagem) ?></div><?php endif; ?>
