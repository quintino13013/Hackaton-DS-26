<?php
session_start();

//desloga do login anterior
session_regenerate_id(true);

// Configurações do banco
$host    = 'localhost:3308';
$db      = 'sistema_locadora';
$user    = 'root';
$pass    = 'etec123';
$charset = 'utf8mb4';

// A String de Conexão (DSN)
// Verifique se não há espaços extras ou hífens no charset
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os dados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepares reais para maior segurança
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    
    //lembrete: se aparecer "não conseguiu achar o driver" lembre de verificar o php.ini e não fuçar no código.
    echo "Erro na conexão: " . $e->getMessage();
}

$erro = "";

    //puxar do post e verificar na tabela pessoa
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($login === '' || $senha === '') {
            $erro = "Preencha login e senha.";
        } else {
            try {
                $sqlPessoa = "SELECT idPessoa, nomePessoa, loginPessoa, emailPessoa, senhaPessoa, tipoPessoa, statusPessoa FROM pessoa WHERE (loginPessoa = :login_nick OR emailPessoa = :login_email) LIMIT 1";

                $stmt = $pdo->prepare($sqlPessoa);
                $stmt->bindParam(':login_nick', $login);
                $stmt->bindParam(':login_email', $login);
                $stmt->execute();
                $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

            //validar credenciais em uma única estrutura
            if (!$pessoa) {
                $erro = "Usuário não encontrado.";
                //echo "Erro real: " . $e->getMessage();

            } elseif ($pessoa['statusPessoa'] !== 'A') {
                $erro = "Conta inativa ou bloqueada.";
            } elseif (!password_verify($senha, $pessoa['senhaPessoa'])) {
                $erro = "Senha incorreta.";
            } else {
                // --- Todas as validações passaram, fazer login ---
                $_SESSION['usuario_id'] = $pessoa['idPessoa'];
                $_SESSION['usuario_tipo'] = 'CL';

                session_regenerate_id(true);
            
                    $proximaPagina = "../index.php"; //isso aq vai pra user comum

                    //verificar se é adm
                    if ($pessoa['tipoPessoa'] === 'AD') {
                        $_SESSION['usuario_id'] = $pessoa['idPessoa'];
                        $_SESSION['usuario_tipo'] = 'AD';

                        $proximaPagina = "../menu.php"; //redireciona pro menu de adm
                    }

                    //faz rediricionamento
                    header("Location: " . $proximaPagina);

                    exit;
            }

        } catch (PDOException $e) {
            $erro = "Erro no sistema ao verificar pessoa.";
            //echo "Erro real: " . $e->getMessage();
        }
    }
}

if (!empty($erro)) {
    echo $erro;
}

?>