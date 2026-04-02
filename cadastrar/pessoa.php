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
    <?php if (isset($GLOBALS['mensagem'])): ?>
        <div class="mensagem" style="margin-bottom: 20px; padding: 10px; border-radius: 5px; <?php echo (strpos($GLOBALS['mensagem'], 'sucesso') !== false || strpos($GLOBALS['mensagem'], 'Cadastro') !== false) ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'; ?>">
            <?php echo htmlspecialchars($GLOBALS['mensagem']); ?>
        </div>
    <?php endif; ?>
    <form action="" method="post">
        <input type="hidden" name="tipo_cadastro" id="tipo_cadastro" value="usuario">

        <label for="nomePessoa" id="nomePessoa">Nome:</label>
        <input type="text" name="nomePessoa" id="nomePessoa" required><br><br>

        <label for="loginPessoa" id="loginPessoa">Login:</label>
        <input type="text" name="loginPessoa" id="loginPessoa" required><br><br>

        <label for="telefonePessoa" id="telefonePessoa">Telefone:</label>
        <input type="tel" name="telefonePessoa" id="telefonePessoa" placeholder="(00) 00000-0000" maxlength="15" oninput="mascaraTelefone(this)" required><br><br>

        <label for="cpfPessoa">CPF:</label>
        <input type="text" name="cpfPessoa" id="cpfPessoa" placeholder="000.000.000-00" maxlength="14" oninput="mascaraCPF(this)" required><br><br>

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
<script src="../javascript/mascaraTelefone.js"></script>
<script src="../javascript/mascaraCpf.js"></script>
</html>