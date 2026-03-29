<?php
include("conex.php");

$sql = "SELECT 
l.idLocacao, 
p.nomePessoa, 
t.nomeTerno, 
l.dataLocacao, 
l.dataPrevista, 
l.dataDevolucao, 
l.statusLocacao
FROM locacoes l
JOIN pessoa p ON l.idCliente = p.idPessoa
JOIN ternos t ON l.idTerno = t.idTerno";

$result = mysqli_query($con, $sql);
?>

<h2>Lista de Locações</h2>
<table border="1">
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Terno</th>
    <th>Data Locação</th>
    <th>Data Prevista</th>
    <th>Data Devolução</th>
    <th>Status</th>
    <th>Ação</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo $row['idLocacao']; ?></td>
    <td><?php echo $row['nomePessoa']; ?></td>
    <td><?php echo $row['nomeTerno']; ?></td>
    <td><?php echo $row['dataLocacao']; ?></td>
    <td><?php echo $row['dataPrevista']; ?></td>
    <td><?php echo $row['dataDevolucao']; ?></td>
    <td><?php echo $row['statusLocacao']; ?></td>
    <td>
        <?php if($row['statusLocacao'] == 'AL'){ ?>
            <a href="devolver.php?id=<?php echo $row['idLocacao']; ?>">Devolver</a>
        <?php } else { echo "Finalizado"; } ?>
    </td>
</tr>
<?php } ?>

</table>

<br>
<a href="cadastrarLocacao.php">Nova Locação</a>