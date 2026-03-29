<?php
include("conex.php");

$mensagem = "";

// Quando enviar o formulário
if($_SERVER['REQUEST_METHOD'] == "POST"){

    $idTerno = $_POST['idTerno'];
    $dataPrevista = $_POST['dataPrevista'];
    $dataHoje = date('Y-m-d');

    // Buscar valor do terno
    $sql = "SELECT * FROM ternos WHERE idTerno='$idTerno'";
    $result = mysqli_query($con, $sql);
    $terno = mysqli_fetch_assoc($result);

    if($terno){

        // Calcular dias
        $diff = strtotime($dataPrevista) - strtotime($dataHoje);
        $dias = ceil($diff / (60*60*24));

        // 🔥 garante mínimo 1 dia
        if($dias <= 0){
            $dias = 1;
        }

        // Calcular valor
        $valorTotal = $dias * $terno['valorLocacao'];

        $mensagem = "
        Terno: {$terno['nomeTerno']} <br>
        Dias: $dias <br>
        Pagamento: {$_POST['pagamento']} <br>
        Valor Total: R$ ".number_format($valorTotal,2,',','.');
    }
}

// lista ternos
$resultTernos = mysqli_query($con, "SELECT * FROM ternos WHERE quantidadeDisponivel > 0");
?>

<h2>Alugar Terno</h2>

<form method="POST">

    Terno:
    <select name="idTerno" required>
        <?php while($t = mysqli_fetch_assoc($resultTernos)){ ?>
            <option value="<?= $t['idTerno'] ?>">
                <?= $t['nomeTerno'] ?> - R$ <?= $t['valorLocacao'] ?>/dia
            </option>
        <?php } ?>
    </select>
    <br><br>

    Data de devolução:
    <input type="date" name="dataPrevista" required>
    <br><br>

    Forma de pagamento:
    <select name="pagamento" required>
        <option>Pix</option>
        <option>Cartão</option>
        <option>Dinheiro</option>
    </select>
    <br><br>

    <input type="submit" value="Calcular">

</form>

<br>

<h3>
<?php 
if($mensagem){
    echo $mensagem;
}
?>
</h3>