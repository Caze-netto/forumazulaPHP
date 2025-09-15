<?php
$host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'forumazuladb';
$username = getenv('DB_USER') ?: 'forumazuladb_user';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=5432;dbname=$db_name;",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com a base de dados: " . $e->getMessage());
}
?>