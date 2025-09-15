<?php
require 'conexao.php';

$stmt = $pdo->query("SELECT id, titulo, data_criacao FROM artigos ORDER BY data_criacao DESC");
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForumAzula - Artigos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="index">
    <header class="site-header">
        <div class="header-content">
            <div class="logo"><a href="index.php">ForumAzula</a></div>
            <nav>
                <a href="admin.php">Área Admin</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <main>
            <h1>Artigos Recentes</h1>
            <?php if (empty($artigos)): ?>
                <p>Nenhum artigo publicado ainda.</p>
            <?php else: ?>
                <ul class="lista-artigos">
                    <?php foreach ($artigos as $artigo): ?>
                        <li>
                            <a href="artigo.php?id=<?= $artigo['id'] ?>">
                                <?= htmlspecialchars($artigo['titulo']) ?>
                            </a>
                            <span class="data-artigo">
                                <?= date("d/m/Y", strtotime($artigo['data_criacao'])) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </main>
        <footer class="site-footer">
            <p>&copy; 2025 ForumAzula | <a href="admin.php">Acesso Restrito</a></p>
        </footer>
    </div>
</body>
</html>