<?php
session_start();
require 'conexao.php';
$erro_login = '';

// Processa a tentativa de login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    $utilizador = $stmt->fetch();
    
    if ($utilizador && password_verify($_POST['password'], $utilizador['palavra_passe'])) {
        $_SESSION['logado'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $erro_login = "Credenciais inválidas.";
    }
}

// Processa o logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Verifica se o utilizador está logado
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    
    // Processa a criação de um novo artigo
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['criar_artigo'])) {
        $titulo = $_POST['titulo'];
        $conteudo = $_POST['conteudo'];
        $stmt = $pdo->prepare("INSERT INTO artigos (titulo, conteudo) VALUES (?, ?)");
        $stmt->execute([$titulo, $conteudo]);
        header("Location: admin.php");
        exit;
    }

    // Processa a exclusão de um artigo
    if (isset($_GET['excluir'])) {
        $id_excluir = filter_input(INPUT_GET, 'excluir', FILTER_VALIDATE_INT);
        if ($id_excluir) {
            $stmt = $pdo->prepare("DELETE FROM artigos WHERE id = ?");
            $stmt->execute([$id_excluir]);
            header("Location: admin.php");
            exit;
        }
    }

    // Busca os artigos existentes para exibir no painel
    $stmt_artigos = $pdo->query("SELECT id, titulo, data_criacao FROM artigos ORDER BY data_criacao DESC");
    $artigos_existentes = $stmt_artigos->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ForumAzula</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="header-content">
        <div class="logo"><a href="index.php">ForumAzula</a></div>
    </div>
</header>

<div class="container" style="max-width:1400px;">
    <?php // Se o utilizador NÃO estiver logado, mostra o formulário de login ?>
    <?php if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true): ?>
    
    <div class="login-container">
        <h2>Acesso Restrito</h2>
        <form method="POST" action="admin.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Palavra-passe" required>
            <?php if ($erro_login): ?><p style="color:#e02424;"><?= htmlspecialchars($erro_login) ?></p><?php endif; ?>
            <button type="submit" name="login" class="btn">Entrar</button>
        </form>
    </div>

    <?php // Se o utilizador ESTIVER logado, mostra o painel de administração ?>
    <?php else: ?>
    
    <main id="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h2>Painel de Controlo</h2>
            <a href="admin.php?logout=true" class="btn btn-delete">Sair</a>
        </div>

        <div class="admin-layout">
            
            <?php // O conteúdo do painel (formulários, listas) entraria aqui ?>
            
        </div> <?php // Fim de .admin-layout ?>
    </main>

    <?php endif; // <-- ESTA LINHA ESTAVA FALTANDO ?>
</div> <?php // Fim de .container ?>

</body>
</html>