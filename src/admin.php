<?php
session_start();
require 'conexao.php';
$erro_login = '';

// --- INÍCIO DO PROCESSO DE LOGIN ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    
    // CORREÇÃO: Usamos trim() para remover espaços em branco acidentais do email e da senha.
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Busca o utilizador na base de dados pelo email fornecido.
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    $utilizador = $stmt->fetch();
    
    // A verificação crucial acontece aqui:
    // 1. A variável $utilizador deve conter dados (ou seja, o email foi encontrado).
    // 2. A função password_verify() deve confirmar que a senha digitada ($password) corresponde ao hash salvo no banco ($utilizador['palavra_passe']).
    if ($utilizador && password_verify($password, $utilizador['palavra_passe'])) {
        
        // Se as duas condições forem verdadeiras, o login é um sucesso.
        $_SESSION['logado'] = true;
        header("Location: admin.php"); // Recarrega a página para mostrar o painel.
        exit;

    } else {
        // Se o email não for encontrado ou a senha não corresponder, define a mensagem de erro.
        $erro_login = "Credenciais inválidas.";
    }
}
// --- FIM DO PROCESSO DE LOGIN ---


// Processa o logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// A partir daqui, o código só executa se o utilizador estiver logado
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    
    // Processa a criação de um novo artigo
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['criar_artigo'])) {
        $titulo = $_POST['titulo'];
        $conteudo = $_POST['conteudo'];
        if (!empty($titulo) && !empty($conteudo)) {
            $stmt = $pdo->prepare("INSERT INTO artigos (titulo, conteudo) VALUES (?, ?)");
            $stmt->execute([$titulo, $conteudo]);
            header("Location: admin.php");
            exit;
        }
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
        <form method="POST" action="admin.php" id="login-form">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Palavra-passe" required>
            <?php if ($erro_login): ?><p style="color:#e02424;"><?= htmlspecialchars($erro_login) ?></p><?php endif; ?>
            <button type="submit" name="login" class="btn">Entrar</button>
        </form>
    </div>

    <?php // Se o utilizador ESTIVER logado, mostra o painel de administração completo ?>
    <?php else: ?>
    
    <main id="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h2>Painel de Controlo</h2>
            <a href="admin.php?logout=true" class="btn btn-delete">Sair</a>
        </div>

        <div class="admin-layout">
            
            <section class="form-section">
                <form method="POST" action="admin.php">
                    <label for="titulo">Título do Artigo</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Um título incrível">
                    
                    <label for="conteudo">Conteúdo (suporta Markdown)</label>
                    <textarea id="conteudo" name="conteudo" rows="20" required placeholder="Escreva seu artigo aqui..."></textarea>
                    
                    <button type="submit" name="criar_artigo" class="btn">Publicar Artigo</button>
                </form>
            </section>
            
            <section class="existing-articles">
                <h3>Artigos Publicados</h3>
                <?php if (empty($artigos_existentes)): ?>
                    <p>Ainda não há artigos. Crie o seu primeiro!</p>
                <?php else: ?>
                    <?php foreach ($artigos_existentes as $artigo): ?>
                        <div class="article-list-item">
                            <div>
                                <p class="article-list-item-title"><?= htmlspecialchars($artigo['titulo']) ?></p>
                                <p class="article-list-item-date">
                                    Publicado em: <?= date('d/m/Y H:i', strtotime($artigo['data_criacao'])) ?>
                                </p>
                            </div>
                            <a href="admin.php?excluir=<?= $artigo['id'] ?>" class="btn btn-delete" onclick="return confirm('Tem a certeza que deseja excluir este artigo?');">
                                Excluir
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

        </div> 
    </main>

    <?php endif; ?>
</div>

</body>
</html>