<?php
require 'conexao.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT titulo, conteudo, data_criacao FROM artigos WHERE id = ?");
$stmt->execute([$id]);
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$artigo) die("Artigo não encontrado");
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($artigo['titulo']) ?> - ForumAzula</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="horizontal-progress"><div class="horizontal-progress-bar" id="progress-bar"></div></div>

<div class="container">
<article>
    <header class="artigo-header">
        <h1 class="artigo-title"><?= htmlspecialchars($artigo['titulo']) ?></h1>
        <p class="artigo-meta">Publicado em: <?= date("d/m/Y", strtotime($artigo['data_criacao'])) ?></p>
    </header>
    <div class="artigo-conteudo" id="artigo-conteudo">
        <?= nl2br(htmlspecialchars($artigo['conteudo'])) ?>
    </div>
</article>
</div>

<script>
const progressBar = document.getElementById('progress-bar');
const artigoConteudo = document.getElementById('artigo-conteudo');

window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;
    const docHeight = artigoConteudo.scrollHeight - window.innerHeight;
    const scrolled = (scrollTop / docHeight) * 100;
    progressBar.style.width = scrolled + '%';
});
</script>
</body>
</html>