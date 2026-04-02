<?php
session_start();
include("../conex.php");

if (!isset($_SESSION['idCliente'])) {
    header("Location: ../login/login.php");
    exit();
}

$idCliente = $_SESSION['idCliente'];

$sql = "SELECT l.*, t.nomeTerno, tam.nomeTamanho, tt.nomeTipoTerno, tec.nomeTipoTecido
        FROM locacoes l
        JOIN ternos t ON l.idTerno = t.idTerno
        LEFT JOIN tamanhos tam ON l.idTamanho = tam.idTamanho
        LEFT JOIN tipo_terno tt ON t.idTipoTerno = tt.idTipoTerno
        LEFT JOIN tipo_tecido tec ON t.idTipoTecido = tec.idTipoTecido
        WHERE l.idCliente = ?
        ORDER BY l.dataLocacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Locações</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Meu Histórico de Locações</h1>
    <table>
        <tr>
            <th>Terno</th>
            <th>Tipo</th>
            <th>Tecido</th>
            <th>Tamanho</th>
            <th>Data Locação</th>
            <th>Data Devolução</th>
            <th>Status</th>
            <th>Valor Total</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nomeTerno']); ?></td>
            <td><?php echo htmlspecialchars($row['nomeTipoTerno']); ?></td>
            <td><?php echo htmlspecialchars($row['nomeTipoTecido']); ?></td>
            <td><?php echo htmlspecialchars($row['nomeTamanho']); ?></td>
            <td><?php echo $row['dataLocacao']; ?></td>
            <td><?php echo $row['dataDevolucao']; ?></td>
            <td><?php echo $row['statusLocacao'] == 'AL' ? 'Alugado' : 'Devolvido'; ?></td>
            <td>R$ <?php echo number_format($row['valorTotal'], 2, ',', '.'); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../index.php">Voltar</a>
</body>
</html>