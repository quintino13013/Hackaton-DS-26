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
    header("Location: mostrarLocacao.php");
    exit();
}

$idLocacao = (int)$_GET['id'];

// Buscar a locação (melhor usar Prepared Statement)
$sql = "SELECT l.idTerno, l.statusLocacao FROM locacoes l WHERE l.idLocacao = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idLocacao);
$stmt->execute();
$result = $stmt->get_result();
$locacao = $result->fetch_assoc();
$stmt->close();

if (!$locacao) {
    header("Location: mostrarLocacao.php");
    exit();
}

if ($locacao['statusLocacao'] != 'AL') {
    header("Location: mostrarLocacao.php");
    exit();
}

$idTerno = $locacao['idTerno'];

// Atualizar status da locação para 'DE' (Devolvido)
$sqlUpdate = "UPDATE locacoes SET statusLocacao = 'DE' WHERE idLocacao = ?";
$stmt = $conn->prepare($sqlUpdate);
$stmt->bind_param("i", $idLocacao);

if ($stmt->execute()) {
    // Aumentar quantidade disponível do terno
    $sqlUpdateTerno = "UPDATE ternos SET quantidadeDisponivel = quantidadeDisponivel + 1 WHERE idTerno = ?";
    $stmt2 = $conn->prepare($sqlUpdateTerno);
    $stmt2->bind_param("i", $idTerno);
    $stmt2->execute();
    $stmt2->close();
    
    $stmt->close();
    $conn->close();
    
    // ←←← LINHA CORRIGIDA:
    header("Location: mostrarLocacao.php");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: mostrarLocacao.php");
    exit();
}
?>