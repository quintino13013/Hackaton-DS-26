<?php
session_start();
include("../conex.php");

header('Content-Type: application/json');

$response = [
    'sucesso' => false,
    'mensagem' => 'Erro desconhecido'
];

if(!isset($_POST['acao']) || $_POST['acao'] != 'locar'){
    $response['mensagem'] = 'Ação inválida';
    echo json_encode($response);
    exit;
}

// Verificar se usuário está logado
if(!isset($_SESSION['usuario_id'])){
    $response['mensagem'] = 'Você precisa estar logado para alugar um terno';
    echo json_encode($response);
    exit;
}

$idCliente = $_SESSION['usuario_id'];
$idTerno = mysqli_real_escape_string($conn, $_POST['idTerno'] ?? '');
$tamanho = mysqli_real_escape_string($conn, $_POST['tamanho'] ?? '');
$dias = (int)($_POST['dias'] ?? 1);
$dataHoje = date('Y-m-d');

// Calcular data de devolução
$dataDevol = date('Y-m-d', strtotime("+$dias days", strtotime($dataHoje)));

// Validações
if(empty($idTerno) || empty($tamanho) || $dias <= 0){
    $response['mensagem'] = 'Campos obrigatórios não preenchidos ou dias inválido';
    echo json_encode($response);
    exit;
}

// Buscar terno
$sql = "SELECT * FROM ternos WHERE idTerno='$idTerno'";
$result = mysqli_query($conn, $sql);
$terno = mysqli_fetch_assoc($result);

if(!$terno){
    $response['mensagem'] = 'Terno não encontrado!';
    echo json_encode($response);
    exit;
}

if($terno['quantidadeDisponivel'] <= 0){
    $response['mensagem'] = 'Terno indisponível!';
    echo json_encode($response);
    exit;
}

// Calcular dias e valor
$valorTotal = $dias * $terno['valorLocacao'];

// Salvar locação
$sqlInsert = "INSERT INTO locacoes (idCliente, idTerno, tamanhoTerno, dataLocacao, dataDevolucao, statusLocacao, valorLocacao)
              VALUES ('$idCliente','$idTerno','$tamanho','$dataHoje','$dataDevol','AL','$valorTotal')";

if(mysqli_query($conn, $sqlInsert)){
    // Atualizar quantidade disponível
    $novaQtd = $terno['quantidadeDisponivel'] - 1;
    mysqli_query($conn, "UPDATE ternos SET quantidadeDisponivel='$novaQtd' WHERE idTerno='$idTerno'");
    
    $response['sucesso'] = true;
    $response['mensagem'] = 'Locação realizada com sucesso!';
    $response['dados'] = [
        'cliente' => $_SESSION['usuario_nome'],
        'terno' => $terno['nomeTerno'],
        'tamanho' => $tamanho,
        'dias' => $dias,
        'valor' => number_format($valorTotal, 2, ',', '.')
    ];
} else {
    $response['mensagem'] = 'Erro ao salvar locação: ' . mysqli_error($conn);
}

echo json_encode($response);
?>
