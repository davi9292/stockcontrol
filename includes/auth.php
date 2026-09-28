<?php
declare(strict_types=1);

function usuario_atual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigir_login(): void
{
    if (!usuario_atual()) redirect('index.php');
}

function eh_administrador(): bool
{
    return (usuario_atual()['perfil'] ?? '') === 'administrador';
}

function exigir_administrador(): void
{
    exigir_login();
    if (!eh_administrador()) {
        http_response_code(403);
        exit('Acesso permitido somente para administrador.');
    }
}
