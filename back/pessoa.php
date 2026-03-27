<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['tipo_cadastro'])) {
    exit;
}

require '../conex.php';

$tipo = $_POST['tipo_cadastro'];

if ($tipo === 'usuario') {

    $nome     = trim($_POST['nomePessoa']     ?? '');
    $login    = trim($_POST['loginPessoa']    ?? '');
    $email    = trim($_POST['emailPessoa']    ?? '');
    $tel      = trim($_POST['telefonePessoa']      ?? '');
    $dataCadastro = date('Y-m-d');
    $senha    = trim($_POST['senhaPessoa']     ?? '');
    $senha2   = $_POST['senhaPessoa_conf']    ?? '';

    // Validações básicas
    if (empty($nome) || empty($login) || empty($email) || empty($senha)) {
        $GLOBALS['mensagem'] = 'Campos obrigatórios não preenchidos (nome, login, email, senha)';
        return;
    }

    if ($senha !== $senha2) {
        $GLOBALS['mensagem'] = 'As senhas não conferem';
        return;
    }

    if (strlen($senha) < 6) {
        $GLOBALS['mensagem'] = 'A senha deve ter pelo menos 6 caracteres';
        return;
    }

    //deixar dificil
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica duplicidade pelo LOGIN
    $sql = "SELECT idPessoa FROM pessoa WHERE loginPessoa = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->bind_result($idExistente);
    $existe = $stmt->fetch();
    $stmt->close();

    if ($existe) {
        $GLOBALS['mensagem'] = "Esse login não está disponível.";
        return;
    }

    //coloca na tabela
    $sql = "INSERT INTO pessoa 
            (nomePessoa, telefonePessoa, loginPessoa, emailPessoa, senhaPessoa, statusPessoa, tipoPessoa, dataCadastro)
            VALUES (?, ?, ?, ?, ?, 'A', 'CL', ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $GLOBALS['mensagem'] = "Erro ao preparar inserção: " . $conn->error;
        return;
    }

    $stmt->bind_param("ssssss", $nome, $tel, $login, $email, $senhaHash, $dataCadastro);

    if ($stmt->execute()) {
        $GLOBALS['mensagem'] = "Cadastro realizado com sucesso!";
    } else {
        $GLOBALS['mensagem'] = "Erro ao salvar no banco: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();