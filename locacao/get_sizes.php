<?php
include("../conex.php");

header('Content-Type: application/json');

$idTerno = $_GET['idTerno'] ?? '';

if (empty($idTerno)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT tam.idTamanho, tam.nomeTamanho, ttam.quantidadeDisponivel 
        FROM terno_tamanhos ttam 
        JOIN tamanhos tam ON ttam.idTamanho = tam.idTamanho 
        WHERE ttam.idTerno = ? AND ttam.quantidadeDisponivel > 0 
        ORDER BY tam.nomeTamanho";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idTerno);
$stmt->execute();
$result = $stmt->get_result();

$sizes = [];
while ($row = $result->fetch_assoc()) {
    $sizes[] = $row;
}

echo json_encode($sizes);
?>