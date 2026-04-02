<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../conex.php");

echo "<h3>Debug Exclusão de Terno</h3>";

// Verifica se é administrador
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    echo "Erro: Você não tem permissão de Admin.<br>";
    echo "Tipo atual: " . ($_SESSION['usuario_tipo'] ?? 'não definido');
    exit();
}

// Verifica se o ID foi enviado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Erro: ID inválido ou não recebido.";
    exit();
}

$id = (int)$_GET['id'];
echo "ID recebido: " . $id . "<br>";

// 1. Excluir todas as locações relacionadas a este terno (histórico incluso)
$sql_delete_locacoes = "DELETE FROM locacoes WHERE idTerno = ?";
$stmt_loc = $conn->prepare($sql_delete_locacoes);
$stmt_loc->bind_param("i", $id);
$stmt_loc->execute();
echo "Locações excluídas: " . $stmt_loc->affected_rows . "<br>";
$stmt_loc->close();

// 2. Excluir registros na tabela terno_tamanhos
$sql_sizes = "DELETE FROM terno_tamanhos WHERE idTerno = ?";
$stmt_sizes = $conn->prepare($sql_sizes);
$stmt_sizes->bind_param("i", $id);
$stmt_sizes->execute();
echo "Registros em terno_tamanhos deletados: " . $stmt_sizes->affected_rows . "<br>";
$stmt_sizes->close();

// 3. Excluir o terno principal
$sql = "DELETE FROM ternos WHERE idTerno = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Erro na preparação do DELETE: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<strong style='color:green'>✅ Terno excluído com sucesso! (" . $stmt->affected_rows . " linha)</strong><br>";
    $stmt->close();
    $conn->close();
    
    // Redireciona para a página principal com mensagem de sucesso
    header("Location: ../index.php?msg=excluido");
    exit();
} else {
    echo "<strong style='color:red'>Erro ao executar DELETE do terno:</strong> " . $stmt->error;
}

$stmt->close();
$conn->close();
?>