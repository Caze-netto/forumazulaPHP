<?php
require 'conexao.php';
$stmt = $pdo->prepare("SELECT id, titulo, data_criacao FROM artigos ORDER BY data_criacao DESC");
$stmt->execute();
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ForumAzula</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
<div class="header-content">
<div class="logo"><a href="index.php">ForumAzula</a></div>
</div>
</header>

<div class="container">
<main>
<h1>Artigos Recentes</h1>
<div class="posts-list">
<?php if(empty($artigos)): ?>
<p>Nenhum artigo publicado.</p>
<?php else: ?>
<?php foreach($artigos as $artigo): ?>
<div class="post-card">
<div class="post-card-meta"><?= date("d/m/Y",strtotime($artigo['data_criacao'])) ?></div>
<h2 class="post-card-title"><a href="artigo.php?id=<?= $artigo['id'] ?>"><?= htmlspecialchars($artigo['titulo']) ?></a></h2>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
</main>
</div>
</body>
</html>