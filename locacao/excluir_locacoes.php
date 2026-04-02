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

// Verificar se foram fornecidos IDs para excluir
if (!isset($_POST['locacoes']) || empty($_POST['locacoes'])) {
    header("Location: mostrarlocacao.php?erro=nenhuma_selecao");
    exit();
}

$locacoes = $_POST['locacoes'];
$totalExcluido = 0;

// Processar exclusão de cada locação selecionada
foreach($locacoes as $id) {
    $id = (int)$id;
    
    // Buscar informações da locação antes de excluir
    $sqlBusca = "SELECT idTerno FROM locacoes WHERE idLocacao = '$id'";
    $resultBusca = mysqli_query($conn, $sqlBusca);
    $locacao = mysqli_fetch_assoc($resultBusca);
    
    if($locacao) {
        // Se a locação estava alugada, devolver a quantidade de ternos
        $sqlStatus = "SELECT statusLocacao FROM locacoes WHERE idLocacao = '$id'";
        $resultStatus = mysqli_query($conn, $sqlStatus);
        $status = mysqli_fetch_assoc($resultStatus);
        
        if($status['statusLocacao'] == 'AL') {
            // Incrementar quantidade disponível
            $sqlUpdate = "UPDATE ternos SET quantidadeDisponivel = quantidadeDisponivel + 1 WHERE idTerno = '" . $locacao['idTerno'] . "'";
            mysqli_query($conn, $sqlUpdate);
        }
        
        // Excluir a locação
        $sqlDelete = "DELETE FROM locacoes WHERE idLocacao = '$id'";
        if(mysqli_query($conn, $sqlDelete)) {
            $totalExcluido++;
        }
    }
}

// Redirecionar com mensagem de sucesso
if($totalExcluido > 0) {
    header("Location: mostrarlocacao.php");
} else {
    header("Location: mostrarlocacao.php");
}
exit();
?>
