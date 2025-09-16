<?php
// Arquivo: index.php
require 'conexao.php';

// Captura e sanitiza os inputs do filtro de datas
$data_inicio = filter_input(INPUT_GET, 'data_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
$data_fim    = filter_input(INPUT_GET, 'data_fim', FILTER_SANITIZE_SPECIAL_CHARS);

// Query base para selecionar artigos, usando TO_CHAR() para PostgreSQL
$query = "SELECT id, titulo, TO_CHAR(data_criacao, 'DD/MM/YYYY') AS data_formatada FROM artigos";
$params = [];
$conditions = [];

// Adiciona as condições de data à query se os filtros foram preenchidos
if ($data_inicio) {
    $conditions[] = "data_criacao >= ?";
    $params[] = $data_inicio; // O tipo de dado 'date' no PostgreSQL aceita o formato 'YYYY-MM-DD'
}
if ($data_fim) {
    $conditions[] = "data_criacao <= ?";
    $params[] = $data_fim;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Ordena os artigos do mais recente para o mais antigo
$query .= " ORDER BY data_criacao DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForumAzula - Artigos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="page-index">

<header class="site-header">
    <div class="header-content">
        <div class="logo"><a href="index.php">ForumAzula</a></div>
    </div>
</header>

<div class="container">
    <main>
        <h1>Artigos Recentes</h1>

        <form method="GET" action="index.php" class="filtro-data">
            <label>De:
                <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio ?? '') ?>">
            </label>
            <label>Até:
                <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim ?? '') ?>">
            </label>
            <button type="submit" class="btn-filtro">Filtrar</button>
            <a href="index.php" class="btn-limpar">Limpar</a>
        </form>

        <?php if (empty($artigos)): ?>
            <p class="nenhum-artigo">Nenhum artigo encontrado para o período selecionado.</p>
        <?php else: ?>
            <ul class="lista-artigos">
                <?php foreach ($artigos as $artigo): ?>
                    <li>
                        <span class="data-artigo"><?= htmlspecialchars($artigo['data_formatada']) ?></span>
                        <a href="artigo.php?id=<?= $artigo['id'] ?>" class="titulo-artigo">
                            <?= htmlspecialchars($artigo['titulo']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> ForumAzula | <a href="admin.php">Acesso Restrito</a></p>
    </footer>
</div>

</body>
</html>