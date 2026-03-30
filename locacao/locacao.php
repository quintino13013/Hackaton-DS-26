<?php
session_start();
include("../conex.php");

$erro = '';
$mensagem = '';

// Simulação de login de cliente (para teste)
if(!isset($_SESSION['idCliente'])){
    // Verificar se cliente teste existe
    $sqlCliente = "SELECT idPessoa FROM pessoa WHERE idPessoa = 1";
    $resultCliente = mysqli_query($conn, $sqlCliente);
    
    if(mysqli_num_rows($resultCliente) == 0){
        // Criar cliente teste se não existir
        $sqlInsertCliente = "INSERT INTO pessoa (idPessoa, nomePessoa, email, telefone) VALUES (1, 'Cliente Teste', 'teste@email.com', '11999999999')";
        mysqli_query($conn, $sqlInsertCliente);
    }
    
    $_SESSION['idCliente'] = 1; // id do cliente teste
    $_SESSION['nomeCliente'] = "Cliente Teste";
}

// ---------- LOCACAO ----------
if(isset($_POST['acao']) && $_POST['acao'] == 'locar'){

    $idCliente = $_SESSION['idCliente'];
    $idTerno = $_POST['idTerno'];
    $tamanho = $_POST['tamanho'];
    $dataDevolucao = $_POST['dataDevolucao'];
    $dataHoje = date('Y-m-d');

    // Buscar terno
    $sql = "SELECT * FROM ternos WHERE idTerno='$idTerno'";
    $result = mysqli_query($conn, $sql);
    $terno = mysqli_fetch_assoc($result);

    if(!$terno){
        $erro = "Terno não encontrado!";
    } elseif($terno['quantidadeDisponivel'] <= 0){
        $erro = "Terno indisponível!";
    } else {
        // Calcular dias e valor
        $diff = strtotime($dataDevolucao) - strtotime($dataHoje);
        $dias = ceil($diff / (60*60*24));
        if($dias <= 0) $dias = 1;
        $valorTotal = $dias * $terno['valorLocacao'];

        // Salvar locação
        $sqlInsert = "INSERT INTO locacoes (idCliente, idTerno, tamanhoTerno, dataLocacao, dataPrevista, dataDevolucao, statusLocacao)
                      VALUES ('$idCliente','$idTerno','$tamanho','$dataHoje','$dataDevolucao','$dataDevolucao','AL')";
        mysqli_query($conn, $sqlInsert);

        // Atualizar quantidade disponível
        $novaQtd = $terno['quantidadeDisponivel'] - 1;
        mysqli_query($conn, "UPDATE ternos SET quantidadeDisponivel='$novaQtd' WHERE idTerno='$idTerno'");

        $mensagem = "
        ✔ Locação realizada com sucesso! <br>
        ✔ Cliente: {$_SESSION['nomeCliente']} <br>
        ✔ Terno: {$terno['nomeTerno']} <br>
        ✔ Tamanho: $tamanho <br>
        ✔ Dias: $dias <br>
        ✔ Valor total: R$ ".number_format($valorTotal,2,',','.');
    }
}

// Buscar ternos para o select
$ternos = mysqli_query($conn, "SELECT * FROM ternos WHERE quantidadeDisponivel > 0");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Locação de Ternos</title>
<style>
body { font-family: Arial; margin: 30px; }
.container { max-width: 500px; margin: auto; }
form { border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
input, select { width: 100%; padding: 6px; margin-top: 4px; }
input[type=submit] { width: auto; margin-top: 10px; cursor: pointer; }
.erro { color: red; margin-bottom: 10px; }
.mensagem { color: green; margin-bottom: 10px; }
</style>
</head>
<body>

<div class="container">
    <h2>Bem-vindo, <?= $_SESSION['nomeCliente'] ?></h2>
    <hr>
    <h3>Alugar um Terno</h3>
    <?php if($erro) echo "<div class='erro'>$erro</div>"; ?>
    <?php if($mensagem) echo "<div class='mensagem'>$mensagem</div>"; ?>

    <form method="post" id="formLocacao">
        <input type="hidden" name="acao" value="locar">

        <label for="idTerno">Escolha o terno:</label>
        <select name="idTerno" id="idTerno" required onchange="atualizarPreco()">
            <option value="">Selecione um terno</option>
            <?php while($t = mysqli_fetch_assoc($ternos)): ?>
                <option value="<?= $t['idTerno'] ?>" data-valor="<?= $t['valorLocacao'] ?>">
                    <?= $t['nomeTerno'] ?> (R$ <?= number_format($t['valorLocacao'],2,',','.') ?>/dia, Disponível: <?= $t['quantidadeDisponivel'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="tamanho">Tamanho:</label>
        <select name="tamanho" required>
            <option value="PP">PP</option>
            <option value="P">P</option>
            <option value="M">M</option>
            <option value="G">G</option>
            <option value="GG">GG</option>
            <option value="XG">XG</option>
            <option value="XGG">XGG</option>
        </select>

        <label for="dataDevolucao">Data de devolução:</label>
        <input type="date" name="dataDevolucao" id="dataDevolucao" required onchange="atualizarPreco()">

        <p><strong>Valor total: R$ <span id="valorTotal">0,00</span></strong></p>

        <input type="submit" value="Alugar">
    </form>
</div>

<script>
function atualizarPreco(){
    const select = document.getElementById('idTerno');
    const dataDev = document.getElementById('dataDevolucao').value;
    const span = document.getElementById('valorTotal');

    if(select.value && dataDev){
        const valorDia = parseFloat(select.selectedOptions[0].dataset.valor);
        const hoje = new Date();
        const devol = new Date(dataDev);
        let dias = Math.ceil((devol - hoje)/(1000*60*60*24));
        if(dias <= 0) dias = 1;
        const total = dias * valorDia;
        span.textContent = total.toFixed(2).replace('.',',');
    } else {
        span.textContent = '0,00';
    }
}
</script>

</body>
</html>
