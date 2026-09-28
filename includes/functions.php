<?php
declare(strict_types=1);

/** Escapa todo texto dinâmico antes de colocá-lo no HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_url(string $caminho = ''): string
{
    return APP_BASE_URL . ($caminho !== '' ? '/' . ltrim($caminho, '/') : '');
}

function redirect(string $caminho): void
{
    header('Location: ' . app_url($caminho));
    exit;
}

function flash(string $tipo, ?string $mensagem = null): ?string
{
    if ($mensagem !== null) {
        $_SESSION['flash'][$tipo] = $mensagem;
        return null;
    }
    $valor = $_SESSION['flash'][$tipo] ?? null;
    unset($_SESSION['flash'][$tipo]);
    return $valor;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $enviado = (string) ($_POST['csrf_token'] ?? '');
    if ($enviado === '' || !hash_equals(csrf_token(), $enviado)) {
        http_response_code(419);
        exit('Sua sessão expirou. Atualize a página e tente novamente.');
    }
}

function validar_produto(array $dados, bool $exigirImagem, array $arquivo): array
{
    $erros = [];
    $nome = trim((string) ($dados['nome'] ?? ''));
    $descricao = trim((string) ($dados['descricao'] ?? ''));
    $quantidadeTexto = trim((string) ($dados['quantidade'] ?? ''));
    $precoTexto = str_replace(',', '.', trim((string) ($dados['preco'] ?? '')));
    $status = (string) ($dados['status'] ?? '');

    if ($nome === '' || mb_strlen($nome) > 160) $erros[] = 'Informe um nome com até 160 caracteres.';
    if ($descricao === '') $erros[] = 'Informe a descrição do produto.';
    if ($quantidadeTexto === '' || filter_var($quantidadeTexto, FILTER_VALIDATE_INT) === false || (int) $quantidadeTexto < 0 || (float) $quantidadeTexto > 4294967295) {
        $erros[] = 'Informe uma quantidade inteira igual ou maior que zero.';
    }
    if ($precoTexto === '' || !is_numeric($precoTexto) || (float) $precoTexto < 0 || (float) $precoTexto > 99999999.99 || strlen(explode('.', $precoTexto)[1] ?? '') > 2) {
        $erros[] = 'Informe um preço válido com até duas casas decimais.';
    }
    if (!in_array($status, ['ativo', 'inativo'], true)) $erros[] = 'Selecione um status válido.';

    $arquivoNovo = isset($arquivo['error']) && $arquivo['error'] !== UPLOAD_ERR_NO_FILE;
    if ($exigirImagem && !$arquivoNovo) $erros[] = 'Selecione uma imagem para o produto.';
    if ($arquivoNovo) {
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            $erros[] = 'Não foi possível receber a imagem enviada.';
        } elseif ((int) $arquivo['size'] > 2 * 1024 * 1024) {
            $erros[] = 'A imagem deve ter no máximo 2 MB.';
        } else {
            $extensoes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
            $extensao = strtolower(pathinfo((string) $arquivo['name'], PATHINFO_EXTENSION));
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file((string) $arquivo['tmp_name']);
            if (!isset($extensoes[$extensao]) || $extensoes[$extensao] !== $mime || @getimagesize((string) $arquivo['tmp_name']) === false) {
                $erros[] = 'Envie uma imagem JPG, PNG ou WEBP válida.';
            }
        }
    }
    return $erros;
}

function salvar_imagem(array $arquivo): string
{
    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
        throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    }
    $extensao = strtolower(pathinfo((string) $arquivo['name'], PATHINFO_EXTENSION));
    $nomeFinal = bin2hex(random_bytes(16)) . '.' . $extensao;
    if (!move_uploaded_file((string) $arquivo['tmp_name'], UPLOAD_DIR . DIRECTORY_SEPARATOR . $nomeFinal)) {
        throw new RuntimeException('Não foi possível salvar a imagem enviada.');
    }
    return $nomeFinal;
}

function remover_imagem(?string $nome): void
{
    if ($nome && preg_match('/\A[a-f0-9]{32}\.(jpg|jpeg|png|webp)\z/i', $nome)) {
        $caminho = UPLOAD_DIR . DIRECTORY_SEPARATOR . $nome;
        if (is_file($caminho)) @unlink($caminho);
    }
}
