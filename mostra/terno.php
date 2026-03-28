<?php
include("conex.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//buscar informações da tabela ternos
$sql = "SELECT * FROM ternos";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Mostrar Ternos</title>

<link rel="stylesheet" href="../css/styleMostra.css">
</head>
<body>

<h1 style="text-align:center;">Ternos</h1>

<div class="cursosOrg">

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        //verificar se tem disponivel
        $disponivel = $row['quantidadeDisponivel'] > 0;
        $statusTexto = $disponivel ? "Disponível" : "Indisponível";
        $statusClasse = $disponivel ? "dispo" : "indispo";
?>

    <div class="cursoBack">
        <div class="img-container">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagemTerno']); ?>" alt="Imagem do terno">
            
            <div class="preco-tag">R$ <?php echo number_format($row['valorLocacao'], 2, ',', '.'); ?>/dia</div>

            <?php if (!$disponivel): ?>
                <div class="alugado-overlay">Alugado</div>
            <?php endif; ?>
        </div>

        <div class="card-content">
            <h3><?php echo $row['nomeTerno']; ?></h3>
            <p class="descricao"><?php echo substr($row['descricaoTerno'], 0, 60); ?>...</p>
            
            <div class="status-container">
                <span class="badge cor"><?php echo $row['tipoTerno']; ?></span>
                <span class="badge <?php echo $statusClasse; ?>"><?php echo $statusTexto; ?></span>
            </div>

            <div class="btn-group">
                <button class="btn-detalhes">👁 Detalhes</button>
            <!-- só aparece se tiver o passe, fala aí-->
                <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'AD'): ?>
                    <a href="../back/excluir.php?id=<?php echo $row['idTerno']; ?>" onclick="return confirm('Excluir este terno?');" style="width:100%">
                        <button class="btn-excluir">Excluir</button>
                    </a>
                <?php else: ?>
                    <button class="btn-alugar" <?php echo !$disponivel ? 'disabled' : ''; ?>>
                        Alugar
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
    }
} else {
    echo "<p style='color:white;'>Nenhum terno cadastrado.</p>";
}
?>
</div>

</body>
</html>