<?php
include("../conex.php");
session_start();

$sql = "SELECT * FROM ternos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossos Ternos</title>
    <link rel="stylesheet" href="../css/styleMostra.css">
</head>
<body>

    <div class="container">
        <h1>Nossos Ternos para Locação</h1>

        <div class="ternos-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $descricao = !empty($row['descricaoTerno']) ? substr($row['descricaoTerno'], 0, 120) . '...' : 'Sem descrição disponível.';
                    $disponivel = $row['quantidadeDisponivel'] > 0 ? 'disponivel' : 'esgotado';
                    $status = $row['quantidadeDisponivel'] > 0 ? 'Disponível' : 'Esgotado';
            ?>
            
            <div class="terno-card">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagemTerno']); ?>" 
                     alt="<?php echo htmlspecialchars($row['nomeTerno']); ?>">

                <div class="card-body">
                    <h2><?php echo htmlspecialchars($row['nomeTerno']); ?></h2>
                    
                    <p class="terno-info"><strong>Tipo:</strong> <?php echo htmlspecialchars($row['tipoTerno']); ?></p>
                    <p class="terno-info"><strong>Tecido:</strong> <?php echo htmlspecialchars($row['tipoTecido']); ?></p>
                    
                    <p class="preco">R$ <?php echo number_format($row['valorLocacao'], 2, ',', '.'); ?></p>
                    
                    <p class="descricao"><?php echo htmlspecialchars($descricao); ?></p>
                    
                    <p class="<?php echo $disponivel; ?>">
                        <?php echo $status; ?> • <?php echo $row['quantidadeDisponivel']; ?> unidades
                    </p>

                    <div style="margin-top: 20px;">
                        <!-- Botão de Locação -->
                        <a href="locar.php?id=<?php echo $row['idTerno']; ?>" class="btn btn-locacao">
                            Quero Locar
                        </a>

                        <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'AD') { ?>
                            <!-- Botão Excluir (só para Admin) -->
                            <a href="../back/excluir.php?id=<?php echo $row['idTerno']; ?>" 
                               onclick="return confirm('Tem certeza que deseja excluir este terno?');"
                               class="btn btn-excluir">
                                Excluir
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php
                }
            } else {
                echo '<p style="grid-column: 1/-1; text-align: center; font-size: 1.3rem; color: #888;">Nenhum terno cadastrado no momento.</p>';
            }
            ?>
        </div>
    </div>

</body>
</html>
