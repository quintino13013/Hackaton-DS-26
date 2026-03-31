<?php
session_start();
require 'conex.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Busca locações do cliente
$sql = "
SELECT t.nomeTerno, l.tamanho, l.valor, l.dataLocacao
FROM locacao l
JOIN ternos t ON l.idTerno = t.idTerno
WHERE l.idPessoa = $usuario_id
ORDER BY l.dataLocacao DESC
";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Histórico de Locações - <?php echo htmlspecialchars($usuario_nome); ?></title>
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
h2 { text-align: center; }
table { width: 80%; margin: 20px auto; border-collapse: collapse; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
th { background-color: #eee; }
</style>
</head>
<body>

<h2>Histórico de Locações de <?php echo htmlspecialchars($usuario_nome); ?></h2>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
    <table>
        <tr>
            <th>Terno</th>
            <th>Tamanho</th>
            <th>Preço (R$)</th>
            <th>Data da Locação</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nomeTerno']); ?></td>
                <td><?php echo htmlspecialchars($row['tamanho']); ?></td>
                <td><?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['dataLocacao'])); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center;">Nenhuma locação encontrada.</p>
<?php endif; ?>

</body>
</html>