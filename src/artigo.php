<?php
require 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT titulo, conteudo, data_criacao FROM artigos WHERE id = ?");
$stmt->execute([$id]);
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);
$titulo_pagina = $artigo ? htmlspecialchars($artigo['titulo']) : "Artigo não encontrado";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?> - ForumAzula</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
</head>
<body id="page-artigo">

<header class="site-header">
    <div class="header-content">
        <div class="logo"><a href="index.php">ForumAzula</a></div>
    </div>
</header>

<!-- Barra de progresso vertical -->
<div class="vertical-progress">
    <div class="vertical-progress-inner" id="vertical-progress-inner"></div>
</div>

<div class="container">
    <main>
        <?php if (!$artigo): ?>
            <h1>Artigo não encontrado</h1>
        <?php else: ?>
            <article>
                <header class="artigo-header">
                    <h1 class="artigo-title"><?= htmlspecialchars($artigo['titulo']) ?></h1>
                    <p class="artigo-meta">Publicado em <?= date("d \d\e F \d\e Y", strtotime($artigo['data_criacao'])) ?></p>
                </header>
                <div class="artigo-conteudo" id="conteudo-renderizado"></div>
                <div id="conteudo-markdown" style="display:none;"><?= htmlspecialchars($artigo['conteudo']) ?></div>
            </article>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <p>&copy; 2025 ForumAzula | <a href="admin.php">Acesso Restrito</a></p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Renderiza Markdown
    const conteudoMarkdown = document.getElementById('conteudo-markdown');
    const conteudoRenderizado = document.getElementById('conteudo-renderizado');
    if (conteudoMarkdown && conteudoRenderizado) {
        conteudoRenderizado.innerHTML = marked.parse(conteudoMarkdown.textContent);
        document.querySelectorAll('.artigo-conteudo pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    }

    // Barra de progresso vertical
    const progressBar = document.getElementById('vertical-progress-inner');
    window.addEventListener('scroll', () => {
        if (!conteudoRenderizado || !progressBar) return;
        const totalHeight = conteudoRenderizado.scrollHeight - window.innerHeight;
        const scrollPos = window.scrollY;
        const progressPercent = Math.min(100, (scrollPos / totalHeight) * 100);
        progressBar.style.height = progressPercent + '%';
    });
});
</script>
</body>
</html>