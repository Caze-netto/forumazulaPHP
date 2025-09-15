<?php
session_start();
require 'conexao.php';
$erro_login='';

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['login'])){
    $email=$_POST['email'];
    $stmt=$pdo->prepare("SELECT * FROM utilizadores WHERE email=?");
    $stmt->execute([$email]);
    $utilizador=$stmt->fetch();
    if($utilizador && password_verify($_POST['password'],$utilizador['palavra_passe'])){
        $_SESSION['logado']=true;
        header("Location:admin.php"); exit;
    }else{ $erro_login="Credenciais inválidas."; }
}

if(isset($_GET['logout'])){ session_destroy(); header("Location:admin.php"); exit; }

if(isset($_SESSION['logado']) && $_SESSION['logado']===true){
    if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['criar_artigo'])){
        $titulo=$_POST['titulo'];
        $conteudo=$_POST['conteudo'];
        $stmt=$pdo->prepare("INSERT INTO artigos (titulo,conteudo) VALUES (?,?)");
        $stmt->execute([$titulo,$conteudo]);
        header("Location:admin.php"); exit;
    }

    if(isset($_GET['excluir'])){
        $id_excluir=filter_input(INPUT_GET,'excluir',FILTER_VALIDATE_INT);
        if($id_excluir){
            $stmt=$pdo->prepare("DELETE FROM artigos WHERE id=?");
            $stmt->execute([$id_excluir]);
            header("Location:admin.php"); exit;
        }
    }

    $stmt_artigos=$pdo->query("SELECT id,titulo,data_criacao FROM artigos ORDER BY data_criacao DESC");
    $artigos_existentes=$stmt_artigos->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - ForumAzula</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
<div class="header-content">
<div class="logo"><a href="index.php">ForumAzula</a></div>
</div>
</header>

<div class="container" style="max-width:1400px;">
<?php if(!isset($_SESSION['logado']) || $_SESSION['logado']!==true): ?>
<div class="login-container">
<h2>Acesso Restrito</h2>
<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Palavra-passe" required>
<?php if($erro_login): ?><p style="color:#e02424;"><?= $erro_login ?></p><?php endif; ?>
<button type="submit" name="login" class="btn">Entrar</button>
</form>
</div>
<?php else: ?>
<main id="admin-content">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
<h2>Painel de Controlo</h2>
<a href="admin.php?logout=true" class="btn btn-delete">Sair</a>
</div>

<div class="admin-layout">