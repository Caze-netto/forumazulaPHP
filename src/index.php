<?php
require 'conexao.php';

// Captura datas do filtro
$data_inicio = filter_input(INPUT_GET, 'data_inicio', FILTER_DEFAULT);
$data_fim    = filter_input(INPUT_GET, 'data_fim', FILTER_DEFAULT);

// Monta query
$query = "SELECT id, titulo, DATE_FORMAT(data_criacao, '%d/%m/%Y %H:%i') AS data_criacao_formatada FROM artigos";
$params = [];

if ($data_inicio && $data_fim) {
    $query .= " WHERE data_criacao BETWEEN ? AND ?";
    $params[] = $data_inicio . " 00:00:00";
    $params[] = $data_fim . " 23:59:59";
}

$query .= " ORDER BY data_criacao DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$stmt = $pdo->prepare("SELECT id, titulo, data_criacao FROM artigos ORDER BY data_criacao DESC");
$stmt->execute();
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ForumAzula - Artigos</title>
<link rel="stylesheet" href="style.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ForumAzula</title>
<link rel="stylesheet" href="style.css">
</head>
<body id="index">

<header class="site-header">
    <div class="header-content">
        <div class="logo"><a href="index.php">ForumAzula</a></div>
    </div>
</header>
<body>
<header class="site-header">
<div class="header-content">
<div class="logo"><a href="index.php">ForumAzula</a></div>
</div>
</header>

<div class="container">
    <main>
        <h1>Artigos Recentes</h1>

        <!-- Formulário de filtro de datas -->
        <form method="GET" action="index.php" class="filtro-data">
            <div class="filtro-wrapper">
                <label>De:
                    <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio ?? '') ?>">
                </label>
                <label>Até:
                    <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim ?? '') ?>">
                </label>
                <button type="submit" class="btn-filtro">Filtrar</button>
                <button type="button" id="btn-limpar" class="btn-limpar">Limpar</button>
            </div>
        </form>

        <?php if (empty($artigos)): ?>
            <p>Nenhum artigo publicado nesse período.</p>
        <?php else: ?>
            <ul class="lista-artigos">
                <?php foreach ($artigos as $artigo): ?>
                    <li>
                        <a href="artigo.php?id=<?= $artigo['id'] ?>">
                            <?= htmlspecialchars($artigo['titulo']) ?>
                        </a>
                        <span class="data-artigo">
                            <?= htmlspecialchars($artigo['data_criacao_formatada']) ?>
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

<script>
document.getElementById('btn-limpar')?.addEventListener('click', () => {
    window.location.href = 'index.php';
});
</script>

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