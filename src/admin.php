<?php
session_start();
require 'conexao.php';
$erro_login = '';

// --- LOGIN ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    $utilizador = $stmt->fetch();
    
    if ($utilizador && password_verify($password, $utilizador['palavra_passe'])) {
        $_SESSION['logado'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $erro_login = "Credenciais inválidas.";
    }
}

// --- LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// --- PAINEL ---
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    
    // Criar artigo
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

    // Excluir artigo
    if (isset($_GET['excluir'])) {
        $id_excluir = filter_input(INPUT_GET, 'excluir', FILTER_VALIDATE_INT);
        if ($id_excluir) {
            $stmt = $pdo->prepare("DELETE FROM artigos WHERE id = ?");
            $stmt->execute([$id_excluir]);
            header("Location: admin.php");
            exit;
        }
    }

    // Buscar artigos
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

    <!-- Highlight.js (tema escuro de código) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
</head>
<body>
<header class="site-header">
    <div class="header-content">
        <div class="logo"><a href="index.php">ForumAzula</a></div>
    </div>
</header>

<div class="container" style="max-width:1400px;">

    <?php if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true): ?>
    
    <!-- Login -->
    <div class="login-container">
        <h2>Acesso Restrito</h2>
        <form method="POST" action="admin.php" id="login-form">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Palavra-passe" required>
            <?php if ($erro_login): ?><p style="color:#e02424;"><?= htmlspecialchars($erro_login) ?></p><?php endif; ?>
            <button type="submit" name="login" class="btn">Entrar</button>
        </form>
    </div>

    <?php else: ?>
    
    <!-- Painel -->
    <main id="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h2>Painel de Controlo</h2>
            <a href="admin.php?logout=true" class="btn btn-delete">Sair</a>
        </div>

        <div class="admin-layout">
            
            <!-- Criar artigo -->
            <section class="form-section">
                <form method="POST" action="admin.php" id="article-form">
                    <label for="titulo">Título do Artigo</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Um título incrível">
                    
                    <label for="conteudo">Conteúdo (Markdown + código)</label>
                    <textarea id="conteudo" name="conteudo" rows="25" required placeholder="Use Markdown. Para código, use ```php ... ```"></textarea>
                    
                    <button type="submit" name="criar_artigo" class="btn">Publicar Artigo</button>
                </form>
            </section>
            
            <!-- Preview -->
            <section class="preview-section">
                <h3>Preview</h3>
                <div id="preview-output" class="artigo-conteudo">
                    <p style="color: var(--color-text-secondary);">O preview do seu artigo aparecerá aqui...</p>
                </div>
            </section>

        </div> 
        
        <!-- Artigos -->
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

    </main>

    <?php endif; ?>
</div>

<!-- Markdown + Highlight.js -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<script>
    // Configurar marked para usar highlight.js
    marked.setOptions({
        highlight: function(code, lang) {
            if (lang && hljs.getLanguage(lang)) {
                return hljs.highlight(code, { language: lang }).value;
            }
            return hljs.highlightAuto(code).value;
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const editor = document.getElementById('conteudo');
        const preview = document.getElementById('preview-output');

        if (editor && preview) {
            const updatePreview = () => {
                const markdownText = editor.value;
                preview.innerHTML = marked.parse(markdownText);
            };

            editor.addEventListener('input', updatePreview);
            updatePreview();
        }
    });
</script>

</body>
</html>