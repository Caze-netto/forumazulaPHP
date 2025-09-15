<?php
require 'conexao.php';
$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id) { header("Location:index.php"); exit; }
$stmt = $pdo->prepare("SELECT titulo, conteudo, data_criacao FROM artigos WHERE id=?");
$stmt->execute([$id]);
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$artigo){ header("Location:index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($artigo['titulo']) ?> - ForumAzula</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
</head>
<body>
<div class="horizontal-progress" id="progress-bar"></div>
<div class="container">
<article class="artigo-conteudo">
<h1><?= htmlspecialchars($artigo['titulo']) ?></h1>
<p class="artigo-meta">Publicado em: <?= date("d/m/Y",strtotime($artigo['data_criacao'])) ?></p>
<div id="conteudo"><?= nl2br(htmlspecialchars($artigo['conteudo'])) ?></div>
</article>
</div>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
const conteudoEl=document.getElementById('conteudo');
conteudoEl.innerHTML = marked.parse(conteudoEl.textContent);
document.querySelectorAll('pre code').forEach(el=>hljs.highlightElement(el));

const progressBar=document.getElementById('progress-bar');
window.addEventListener('scroll', ()=>{
  const scrollTop=window.scrollY;
  const docHeight=document.body.scrollHeight-window.innerHeight;
  const scrolled=(scrollTop/docHeight)*100;
  progressBar.style.width=scrolled+"%";
});
</script>
</body>
</html>