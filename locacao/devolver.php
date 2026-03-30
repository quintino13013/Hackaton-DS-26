<?php
include("../conex.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se é administrador
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    header("Location: ../index.php");
    exit();
}

// Verificar se foi fornecido um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: mostrarlocacao.php");
    exit();
}

$idLocacao = (int)$_GET['id'];

// Buscar a locação
$sql = "SELECT l.idTerno, l.statusLocacao FROM locacoes l WHERE l.idLocacao = '$idLocacao'";
$result = mysqli_query($conn, $sql);
$locacao = mysqli_fetch_assoc($result);

if (!$locacao) {
    header("Location: mostrarlocacao.php?erro=locacao_nao_encontrada");
    exit();
}

if ($locacao['statusLocacao'] != 'AL') {
    header("Location: mostrarlocacao.php?erro=locacao_ja_devolvida");
    exit();
}

$idTerno = $locacao['idTerno'];

// Atualizar status da locação para 'DE' (Devolvido)
$sqlUpdate = "UPDATE locacoes SET statusLocacao = 'DE' WHERE idLocacao = '$idLocacao'";
if (mysqli_query($conn, $sqlUpdate)) {
    // Aumentar quantidade disponível do terno
    $sqlUpdateTerno = "UPDATE ternos SET quantidadeDisponivel = quantidadeDisponivel + 1 WHERE idTerno = '$idTerno'";
    mysqli_query($conn, $sqlUpdateTerno);
    
    header("Location: mostrarlocacao.php?sucesso=devolucao_realizada");
    exit();
} else {
    header("Location: mostrarlocacao.php?erro=erro_devolucao");
    exit();
}
?>
