<?php
require 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: index.php"); exit; }

// Seleciona artigo com data formatada
$stmt = $pdo->prepare("
    SELECT 
        titulo, 
        conteudo, 
        DATE_FORMAT(data_criacao, '%d/%m/%Y %H:%i') AS data_criacao_formatada 
    FROM artigos 
    WHERE id = ?
");
$stmt->execute([$id]);
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artigo) { header("Location: index.php"); exit; }

$titulo_pagina = htmlspecialchars($artigo['titulo']);
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

<div class="reading-progress-bar" id="progress-bar"></div>

<div class="container">
    <main>
        <article>
            <header class="artigo-header">
                <h1 class="artigo-title"><?= $titulo_pagina ?></h1>
                <p class="artigo-meta">Publicado em <?= htmlspecialchars($artigo['data_criacao_formatada']) ?></p>
            </header>
            <div class="artigo-conteudo" id="conteudo-renderizado"></div>
            <div id="conteudo-markdown" style="display:none;"><?= htmlspecialchars($artigo['conteudo']) ?></div>
        </article>
        <a href="index.php" class="voltar-link">&larr; Voltar para a lista</a>
    </main>

    <footer class="site-footer">
        <p>&copy; 2025 ForumAzula | <a href="admin.php">Acesso Restrito</a></p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Render Markdown
    const conteudoMarkdown = document.getElementById('conteudo-markdown');
    const conteudoRenderizado = document.getElementById('conteudo-renderizado');
    if (conteudoMarkdown && conteudoRenderizado) {
        conteudoRenderizado.innerHTML = marked.parse(conteudoMarkdown.textContent);
        document.querySelectorAll('.artigo-conteudo pre code').forEach(block => hljs.highlightElement(block));
    }

    // Barra de progresso ao rolar
    const progressBar = document.getElementById('progress-bar');
    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY;
        const docHeight = document.body.scrollHeight - window.innerHeight;
        const scrolled = (scrollTop / docHeight) * 100;
        progressBar.style.width = scrolled + "%";
    });
});
</script>

</body>
</html>