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


    $pdo->exec("
        CREATE TABLE IF NOT EXISTS utilizadores (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            palavra_passe VARCHAR(255) NOT NULL
        )
    ");


    $pdo->exec("
        CREATE TABLE IF NOT EXISTS artigos (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            conteudo TEXT NOT NULL,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");


    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilizadores WHERE email = ?");
    $stmt->execute(['admin@forumazula.com']);
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('azula123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO utilizadores (email, palavra_passe) VALUES (?, ?)");
        $stmt->execute(['admin@forumazula.com', $hash]);
    }

} catch (PDOException $e) {
    die("Erro de conexão ou criação de tabelas: " . $e->getMessage());
}
?>
