<?php
session_start();
require 'conexao.php';

$erro_login = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
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

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['criar_artigo'])) {
        $titulo = trim($_POST['titulo']);
        $conteudo = trim($_POST['conteudo']);

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $caminho_upload = 'uploads/';
            $nome_ficheiro = uniqid() . '_' . basename($_FILES['imagem']['name']);
            $caminho_completo = $caminho_upload . $nome_ficheiro;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
                $conteudo .= "\n\n![Imagem do artigo](" . htmlspecialchars($caminho_completo) . ")";
            }
        }

        $stmt = $pdo->prepare("INSERT INTO artigos (titulo, conteudo) VALUES (?, ?)");
        $stmt->execute([$titulo, $conteudo]);
        header("Location: admin.php");
        exit;
    }

    if (isset($_GET['excluir'])) {
        $id_excluir = filter_input(INPUT_GET, 'excluir', FILTER_VALIDATE_INT);
        if ($id_excluir) {
            $stmt = $pdo->prepare("DELETE FROM artigos WHERE id = ?");
            $stmt->execute([$id_excluir]);
            header("Location: admin.php");
            exit;
        }
    }

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
<body id="admin">
    <header class="site-header">
        <div class="header-content"><div class="logo"><a href="index.php">ForumAzula</a></div></div>
    </header>

    <div class="container" style="max-width: 1400px;">
        <?php if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true): ?>
            <div class="login-container">
                <h2>Acesso Restrito</h2>
                <form id="login-form" method="POST" action="admin.php">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Palavra-passe" required>
                    <?php if($erro_login): ?><p style="color: #e02424;"><?= $erro_login ?></p><?php endif; ?>
                    <button type="submit" name="login" class="btn">Entrar</button>
                </form>
            </div>
        <?php else: ?>
            <main id="admin-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Painel de Controlo</h2>
                    <a href="admin.php?logout=true" class="btn btn-delete">Sair</a>
                </div>

                <div class="admin-layout">
                    <form method="POST" action="admin.php" class="form-section" enctype="multipart/form-data">
                        <h3>Criar Novo Artigo</h3>
                        <input type="text" id="titulo" name="titulo" required placeholder="Título do artigo">
                        <div>
                            <label for="conteudo">Conteúdo</label>
                            <div class="editor-toolbar">
                                <button type="button" class="toolbar-btn" data-action="heading">H2</button>
                                <button type="button" class="toolbar-btn" data-action="bold"><b>B</b></button>
                                <button type="button" class="toolbar-btn" data-action="italic"><i>I</i></button>
                                <button type="button" class="toolbar-btn" data-action="quote">"</button>
                                <button type="button" class="toolbar-btn" data-action="code">{}</button>
                                <button type="button" class="toolbar-btn" data-action="link">Link</button>
                            </div>
                            <textarea id="conteudo" name="conteudo" required placeholder="Escreva em Markdown..."></textarea>
                        </div>
                        <div class="form-upload-wrapper">
                            <label for="imagem">Anexar Imagem (opcional)</label>
                            <input type="file" name="imagem" id="imagem" accept="image/*">
                        </div>
                        <button type="submit" name="criar_artigo" class="btn">Guardar Artigo</button>
                    </form>

                    <section class="preview-section">
                        <h3>Pré-visualização</h3>
                        <div id="preview-content" class="artigo-conteudo"></div>
                    </section>
                </div>

                <section class="existing-articles">
                    <h3 style="margin-top:0;">Gerir Artigos Existentes</h3>
                    <?php foreach ($artigos_existentes as $artigo): ?>
                        <div class="article-list-item">
                            <div>
                                <p class="article-list-item-title"><?= htmlspecialchars($artigo['titulo']) ?></p>
                                <p class="article-list-item-date">Publicado em: <?= date("d/m/Y", strtotime($artigo['data_criacao'])) ?></p>
                            </div>
                            <a href="admin.php?excluir=<?= $artigo['id'] ?>" class="btn btn-delete" onclick="return confirm('Tem a certeza que quer excluir este artigo?');">Excluir</a>
                        </div>
                    <?php endforeach; ?>
                </section>
            </main>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
        <script src="admin-ux.js"></script>
    <?php endif; ?>
</body>
</html>