<?php
require 'conexao.php';

$data_inicio = filter_input(INPUT_GET, 'data_inicio', FILTER_SANITIZE_STRING);
$data_fim    = filter_input(INPUT_GET, 'data_fim', FILTER_SANITIZE_STRING);


$query = "SELECT id, titulo, data_criacao FROM artigos";
$params = [];

if ($data_inicio && $data_fim) {
    $query .= " WHERE data_criacao BETWEEN ? AND ?";
    $params[] = $data_inicio . " 00:00:00";
    $params[] = $data_fim . " 23:59:59";
}

$query .= " ORDER BY data_criacao DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
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
        </div>
    </header>

    <div class="container">
        <main>
            <h1>Artigos Recentes</h1>

            <!-- Formulário de filtro de data -->
            <form method="GET" action="index.php" class="filtro-data">
                <label>De: <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio ?? '') ?>"></label>
                <label>Até: <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim ?? '') ?>"></label>
                <button type="submit">Filtrar</button>
                <a href="index.php" class="btn-limpar">Limpar</a>
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