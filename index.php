<?php
require 'conexao.php';

$stmt = $pdo->query("SELECT id, titulo, conteudo, data_criacao FROM artigos ORDER BY data_criacao DESC");
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForumAzula</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body id="home">
    <header class="site-header">
        <div class="header-content">
            <div class="logo"><a href="index.php">ForumAzula</a></div>
        </div>
    </header>
    <div class="container">
        <main>
            <div class="posts-list">
                <?php if (empty($artigos)): ?>
                    <div class="empty-state"><h3>Nenhum artigo encontrado.</h3></div>
                <?php else: ?>
                    <?php foreach ($artigos as $artigo): ?>
                        <?php
                            $excerto = mb_substr(strip_tags($artigo['conteudo']), 0, 150) . '...';
                            $data_formatada = date("d \d\e F \d\e Y", strtotime($artigo['data_criacao']));
                        ?>
                        <a href="artigo.php?id=<?= htmlspecialchars($artigo['id']) ?>" style="text-decoration: none; color: inherit;">
                            <article class="post-card">
                                <div class="post-card-meta">
                                    <time><?= $data_formatada ?></time>
                                </div>
                                <h3 class="post-card-title"><?= htmlspecialchars($artigo['titulo']) ?></h3>
                                <p class="post-card-excerpt"><?= htmlspecialchars($excerto) ?></p>
                            </article>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
        <footer class="site-footer">
            <p>&copy; 2025 ForumAzula | <a href="admin.php">Acesso Restrito</a></p>
        </footer>
    </div>
</body>
</html>