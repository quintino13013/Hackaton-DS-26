<?php
include("../conex.php");

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
?>

    <div class="cursoBack">

        <p><b>Imagem:</b></p>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagemTerno']); ?>" alt="Imagem do terno" style="max-width:200px;">
        <p></p>
        
        <h2><?php echo $row['nomeTerno']; ?></h2>

        <p><?php echo $row['nomeTerno']; ?></p>
        <p><?php echo substr($row['descricaoCurso'], 0, 80); ?>...</p>
        <p><?php echo $row['tipoTerno']; ?></p>
        <p><?php echo $row['tipoTecido']; ?></p>
        <p><?php echo $row['valorLocacao']; ?></p>
        <p><?php echo $row['quantidadeDisponivel']; ?></p>
        <p><?php echo substr($row['descricaoTerno'], 0, 80); ?>...</p>
        
        <!-- BOTÃO ACESSAR -->
        <a href="<?php echo $row['linkCurso']; ?>" target="_blank">
            <button style="background:blue; color:white;">
                Acessar Curso
            </button>
        </a>

<?php
//só aparece se tiver o passe, fala ae

if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'AD') {
    
        //BOTÃO EXCLUIR
        echo "<a href=\"../back/excluir.php?id=" . $row['idTerno'] . "\"
           onclick=\"return confirm('Tem certeza que deseja excluir?');\">
            <button style=\"background:red; color:white;\">
                Excluir
            </button>
        </a>";
}
?>


<?php
    }
} else {
    echo "<p>Nenhum curso cadastrado.</p>";
}
?>

</div>

</body>
</html>