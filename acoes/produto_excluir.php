<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/bootstrap.php';
exigir_administrador();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
verify_csrf();
$id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
if (!$id) { flash('error', 'Produto inválido.'); redirect('pages/produtos.php'); }
$stmt = $pdo->prepare("UPDATE produtos SET status = 'inativo', excluido_em = CURRENT_TIMESTAMP WHERE id = :id AND excluido_em IS NULL");
$stmt->execute(['id' => $id]);
flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Produto excluído com sucesso.' : 'Produto não encontrado ou já excluído.');
redirect('pages/produtos.php');
