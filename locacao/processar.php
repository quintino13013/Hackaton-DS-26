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

$idCliente = $_SESSION['usuario_id'];
$idTerno = mysqli_real_escape_string($conn, $_POST['idTerno'] ?? '');
$idTamanho = mysqli_real_escape_string($conn, $_POST['tamanho'] ?? '');
$dias = (int)($_POST['dias'] ?? 1);
$metodoPagamento = mysqli_real_escape_string($conn, $_POST['metodoPagamento'] ?? '');
$dataHoje = date('Y-m-d');

// Calcular data de devolução
$dataDevol = date('Y-m-d', strtotime("+$dias days", strtotime($dataHoje)));

// Validações
if(empty($idTerno) || empty($idTamanho) || empty($metodoPagamento) || $dias <= 0){
    $response['mensagem'] = 'Campos obrigatórios não preenchidos ou dias inválido';
    echo json_encode($response);
    exit;
}

// Verificar disponibilidade do tamanho
$sql_check = "SELECT quantidadeDisponivel FROM terno_tamanhos WHERE idTerno = '$idTerno' AND idTamanho = '$idTamanho' AND quantidadeDisponivel > 0";
$result_check = mysqli_query($conn, $sql_check);
$check = mysqli_fetch_assoc($result_check);

if(!$check){
    $response['mensagem'] = 'Tamanho não disponível para este terno!';
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

// Calcular dias e valor
$valorTotal = $dias * $terno['valorLocacao'];

// Salvar locação
$sqlInsert = "INSERT INTO locacoes (idCliente, idTerno, idTamanho, dataLocacao, dataDevolucao, metodoPagamento, statusLocacao, valorTotal)
              VALUES ('$idCliente','$idTerno','$idTamanho','$dataHoje','$dataDevol','$metodoPagamento','AL','$valorTotal')";

if(mysqli_query($conn, $sqlInsert)){
    // Atualizar quantidade disponível no terno_tamanhos
    $novaQtd = $check['quantidadeDisponivel'] - 1;
    mysqli_query($conn, "UPDATE terno_tamanhos SET quantidadeDisponivel='$novaQtd' WHERE idTerno='$idTerno' AND idTamanho='$idTamanho'");
    
    // Get size name
    $sql_size = "SELECT nomeTamanho FROM tamanhos WHERE idTamanho = '$idTamanho'";
    $result_size = mysqli_query($conn, $sql_size);
    $size_row = mysqli_fetch_assoc($result_size);
    $nomeTamanho = $size_row['nomeTamanho'];
    
    $response['sucesso'] = true;
    $response['mensagem'] = 'Locação realizada com sucesso!';
    $response['dados'] = [
        'cliente' => $_SESSION['usuario_nome'],
        'terno' => $terno['nomeTerno'],
        'tamanho' => $nomeTamanho,
        'dias' => $dias,
        'valor' => number_format($valorTotal, 2, ',', '.')
    ];
} else {
    $response['mensagem'] = 'Erro ao salvar locação: ' . mysqli_error($conn);
}

echo json_encode($response);
?>
