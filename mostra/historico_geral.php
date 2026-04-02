<?php
session_start();
include("../conex.php");

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    header("Location: ../index.php");
    exit();
}

$sql = "SELECT l.*, p.nomePessoa, p.email, t.nomeTerno, tam.nomeTamanho
        FROM locacoes l
        JOIN pessoa p ON l.idCliente = p.idPessoa
        JOIN ternos t ON l.idTerno = t.idTerno
        LEFT JOIN tamanhos tam ON l.idTamanho = tam.idTamanho
        ORDER BY l.dataLocacao DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico Geral de Locações</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Histórico Geral de Locações</h1>
    <table>
        <tr>
            <th>Cliente</th>
            <th>Email</th>
            <th>Modelo</th>
            <th>Tamanho</th>
            <th>Data Retirada</th>
            <th>Data Entrega</th>
            <th>Meio de Pagamento</th>
            <th>Devolvido</th>
            <th>Inadimplente</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nomePessoa']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['nomeTerno']); ?></td>
            <td><?php echo htmlspecialchars($row['nomeTamanho']); ?></td>
            <td><?php echo $row['dataLocacao']; ?></td>
            <td><?php echo $row['dataDevolucao']; ?></td>
            <td><?php echo $row['metodoPagamento']; ?></td>
            <td><?php echo $row['statusLocacao'] == 'DV' ? 'Sim' : 'Não'; ?></td>
            <td><?php echo $row['statusLocacao'] == 'IN' ? 'Sim' : 'Não'; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../index.php">Voltar</a>
</body>
</html>