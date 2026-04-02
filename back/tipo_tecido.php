<?php
header('Content-Type: application/json');
require '../conex.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['ok' => false, 'mensagem' => 'Método inválido']));
}

$nome = trim($_POST['nomeTipoTecido'] ?? '');

if (empty($nome)) {
    exit(json_encode(['ok' => false, 'mensagem' => 'Nome obrigatório']));
}

// Check duplicate
$sql = "SELECT idTipoTecido FROM tipo_tecido WHERE nomeTipoTecido = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nome);
$stmt->execute();
$stmt->bind_result($idExistente);
$existe = $stmt->fetch();
$stmt->close();

if ($existe) {
    exit(json_encode(['ok' => false, 'mensagem' => 'Tipo de tecido já existe']));
}

// Insert
$sql = "INSERT INTO tipo_tecido (nomeTipoTecido) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nome);

if ($stmt->execute()) {
    exit(json_encode(['ok' => true, 'mensagem' => 'Tipo de tecido cadastrado com sucesso!']));
} else {
    exit(json_encode(['ok' => false, 'mensagem' => 'Erro ao salvar: ' . $stmt->error]));
}

$stmt->close();
$conn->close();
?>
