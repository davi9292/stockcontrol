<?php
declare(strict_types=1);
require_once __DIR__ . '/config/bootstrap.php';

if (!empty($_SESSION['usuario'])) {
    redirect('pages/produtos.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $erro = 'Informe o e-mail e a senha.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, email, senha_hash, perfil, ativo FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && (int) $usuario['ativo'] === 1 && password_verify($senha, $usuario['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = [
                'id' => (int) $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'perfil' => $usuario['perfil'],
            ];
            redirect('pages/produtos.php');
        }
        $erro = 'E-mail ou senha inválidos.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | StockControl</title>
    <link rel="icon" href="<?= e(app_url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body class="login-page">
<main class="login-shell">
    <section class="login-card">
        <img class="login-logo" src="<?= e(app_url('assets/img/logo.svg')) ?>" alt="StockControl — controle de estoque">
        <h1 class="h4 mt-4 mb-1">Acesse sua conta</h1>
        <p class="text-secondary mb-4">Entre para consultar o estoque.</p>
        <?php if ($erro !== ''): ?><div class="alert alert-danger" role="alert"><?= e($erro) ?></div><?php endif; ?>
        <?php if ($mensagem = flash('success')): ?><div class="alert alert-success" role="status"><?= e($mensagem) ?></div><?php endif; ?>
        <form method="post" action="<?= e(app_url('index.php')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input class="form-control" type="email" id="email" name="email" autocomplete="username" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <input class="form-control" type="password" id="senha" name="senha" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Entrar</button>
        </form>
    </section>
</main>
</body>
</html>
