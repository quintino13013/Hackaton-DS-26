<?php

//require '../conex.php';

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$erro = '';
$mensagem = '';
$tipo_msg = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../back/login.php';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Cursos</title>
    <link rel="stylesheet" href="../css/styleLG.css">
</head>
<body>

<div class="container">
    <h2>Entrar</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <label for="login">Nick ou E-mail</label>
        <input type="text" name="login" id="login" required autofocus placeholder="Seu nick ou e-mail"><br><br>

        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" required>

        <button type="submit">Entrar</button>
        <a href="../index.php">Home</a>

    </form>
</div>

</body>
</html>