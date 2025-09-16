<?php
require 'conexao.php';

// ADICIONADO: Define o locale para português para formatar a data corretamente
setlocale(LC_TIME, 'pt_PT', 'pt_PT.utf-8', 'pt_BR', 'pt_BR.utf-8', 'portuguese');

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

    <div class="reading-progress-bar"></div>

    <div class="container">
        <main>
            <?php if (!$artigo): ?>
                <h1>Artigo não encontrado</h1>
            <?php else: ?>
                <article>
                    <header class="artigo-header">
                        <h1 class="artigo-title"><?= htmlspecialchars($artigo['titulo']) ?></h1>
                        <p class="artigo-meta">Publicado em <?= strftime('%d de %B de %Y', strtotime($artigo['data_criacao'])) ?></p>
                    </header>
                    <div class="artigo-conteudo" id="conteudo-renderizado"></div>
                    <div id="conteudo-markdown" style="display:none;"><?= htmlspecialchars($artigo['conteudo']) ?></div>
                </article>
            <?php endif; ?>
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
            // Lógica existente para renderizar o Markdown
            const conteudoMarkdown = document.getElementById('conteudo-markdown');
            if (conteudoMarkdown) {
                const conteudoRenderizado = document.getElementById('conteudo-renderizado');
                conteudoRenderizado.innerHTML = marked.parse(conteudoMarkdown.textContent);
                document.querySelectorAll('.artigo-conteudo pre code').forEach((block) => {
                    hljs.highlightBlock(block);
                });
            }

            // --- ADICIONADO: Lógica da barra de progresso de leitura ---
            const progressBar = document.querySelector('.reading-progress-bar');
            const articleElement = document.querySelector('article');

            if (progressBar && articleElement) {
                const updateProgressBar = () => {
                    const articleTop = articleElement.offsetTop;
                    const articleHeight = articleElement.scrollHeight;
                    const viewportHeight = window.innerHeight;
                    
                    // A distância total que pode ser rolada dentro do artigo
                    const scrollableHeight = articleHeight - viewportHeight;
                    
                    // A posição de rolagem atual relativa ao topo do artigo
                    const currentScrollInArticle = window.scrollY - articleTop;

                    if (currentScrollInArticle < 0 || scrollableHeight <= 0) {
                        progressBar.style.width = '0%';
                        return;
                    }
                    
                    // Calcula a percentagem de progresso, limitando a 100%
                    const progress = Math.min((currentScrollInArticle / scrollableHeight) * 100, 100);
                    
                    progressBar.style.width = `${progress}%`;
                };

                window.addEventListener('scroll', updateProgressBar, { passive: true });
                updateProgressBar(); // Chama uma vez para o caso da página carregar já rolada
            }
        });
    </script>
</body>
</html>