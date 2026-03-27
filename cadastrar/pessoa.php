<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../back/pessoa.php';  
    }
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar usuário</title>
    <link rel="stylesheet" href="../css/styleLG.css">
</head>
<body>
    <h1>Cadastrar usuário</h1>

<div class="cadastro-container">
    <form action="" method="post">
        <input type="hidden" name="tipo_cadastro" id="tipo_cadastro" value="usuario">

        <label for="nomePessoa" id="nomePessoa">Nome:</label>
        <input type="text" name="nomePessoa" id="nomePessoa" required><br><br>

        <label for="loginPessoa" id="loginPessoa">Login:</label>
        <input type="text" name="loginPessoa" id="loginPessoa" required><br><br>

        <label for="telefonePessoa" id="telefonePessoa">Telefone:</label>
        <input type="tel" name="telefonePessoa" id="telefonePessoa" placeholder="(00) 00000-0000" required><br><br>

        <label for="emailPessoa" id="emailPessoa">Email:</label>
        <input type="email" name="emailPessoa" id="emailPessoa" placeholder="exemplo@gmail.com" required><br><br>

        <label for="senhaPessoa" id="senhaPessoa">Senha:</label>
        <input type="password" name="senhaPessoa" id="senhaPessoa" required><br><br>

        <label for="senhaPessoa_conf" id="senhaPessoa_conf">Confirme a Senha:</label>
        <input type="password" name="senhaPessoa_conf" id="senhaPessoa_conf" required><br><br>

        <button type="submit">Cadastrar</button>

    </form>
</div>

</body>
</html>