<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    header("Location: ../index.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../back/tamanho.php';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tamanho</title>
    <link rel="stylesheet" href="../css/styleLG.css">
</head>
<body>
    <h1>Cadastrar Tamanho</h1>

    <div class="cadastro-container">
        <form action="" method="post">
            <label for="nomeTamanho">Nome do Tamanho:</label>
            <input type="text" name="nomeTamanho" id="nomeTamanho" required><br><br>

            <button type="submit">Cadastrar</button>
            <a href="../index.php">Voltar</a>
        </form>
    </div>

</body>
</html>