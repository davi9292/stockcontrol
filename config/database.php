<?php
declare(strict_types=1);

// Ajuste usuário, senha e host conforme a instalação local do MySQL/MariaDB.
$dbHost = '127.0.0.1';
$dbName = 'stockcontrol';
$dbUser = 'root';
$dbPassword = '';
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados. Confira config/database.php e importe script.sql.');
}
