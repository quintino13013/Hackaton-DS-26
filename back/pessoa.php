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
    $cpf = trim($_POST['cpfPessoa']     ?? '');
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

    if (strlen($senha) < 8) {
        $GLOBALS['mensagem'] = 'A senha deve ter pelo menos 8 caracteres';
        return;
    }

    //checar senha fraca
    if (!preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $GLOBALS['mensagem'] = 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula e um número';
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
            (nomePessoa, telefonePessoa, cpfPessoa, loginPessoa, emailPessoa, senhaPessoa, statusPessoa, tipoPessoa, dataCadastro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $GLOBALS['mensagem'] = "Erro ao preparar inserção: " . $conn->error;
        return;
    }

    $status = 'A';
    $tipo = 'CL';
    $stmt->bind_param("sssssssss", $nome, $tel, $cpf, $login, $email, $senhaHash, $status, $tipo, $dataCadastro);

    if ($stmt->execute()) {
        $GLOBALS['mensagem'] = "Cadastro realizado com sucesso!";
        header("Location: ../login/login.php");
        exit;
    } else {
        $GLOBALS['mensagem'] = "Erro ao salvar no banco: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();