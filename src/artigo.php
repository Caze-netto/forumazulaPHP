<?php

require 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}


$query = "
    SELECT 
        titulo, 
        conteudo, 
        TO_CHAR(data_criacao, 'DD \"de\" FMMonth \"de\" YYYY \"às\" HH24:MI') AS data_formatada 
    FROM artigos 
    WHERE id = ?
";

$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artigo) {
    header("Location: index.php");
    exit;
}

$titulo_pagina = htmlspecialchars($artigo['titulo']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
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

<div class="reading-progress-bar"></div>

<div class="container">
    <main>
        <article>
            <header class="artigo-header">
                <h1 class="artigo-titulo"><?= $titulo_pagina ?></h1>
                <p class="artigo-meta">Publicado em <?= htmlspecialchars($artigo['data_formatada']) ?></p>
            </header>
            
            <div class="artigo-conteudo" id="conteudo-renderizado"></div>
            <div id="conteudo-markdown" style="display:none;"><?= htmlspecialchars($artigo['conteudo']) ?></div>
            
        </article>
        <a href="index.php" class="voltar-link">&larr; Voltar para a lista</a>
    </main>

    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> ForumAzula | <a href="admin.php">Acesso Restrito</a></p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    const markdownContent = document.getElementById('conteudo-markdown')?.textContent;
    const renderTarget = document.getElementById('conteudo-renderizado');
    if (markdownContent && renderTarget) {
        renderTarget.innerHTML = marked.parse(markdownContent);
        
        renderTarget.querySelectorAll('pre code').forEach(block => {
            hljs.highlightElement(block);
        });
    }

    const progressBar = document.querySelector('.reading-progress-bar');
    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = (scrollTop / docHeight) * 100;
        progressBar.style.width = scrolled + "%";
    });
});
</script>

</body>
</html>