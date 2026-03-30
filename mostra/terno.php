<?php

include("conex.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sql = "SELECT * FROM ternos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossos Ternos</title>
    <link rel="stylesheet" href="css/styleMostra.css">
    <link rel="stylesheet" href="../css/styleCardLocar.css">
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
                        <button onclick="abrirModalLocacao(<?php echo $row['idTerno']; ?>, '<?php echo htmlspecialchars($row['nomeTerno']); ?>', <?php echo $row['valorLocacao']; ?>)" class="btn btn-locacao">
                            Quero Alugar
                        </button>

                        <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'AD') { ?>
                            <!--botão que exclui, só aparece pra quem tem o passe, fala aí-->
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

    <!-- Modal de Locação -->
    <div id="modalLocacao" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Alugar Terno</h2>
                <span class="close" onclick="fecharModalLocacao()">&times;</span>
            </div>

            <form method="post" class="modal-form" onsubmit="salvarLocacao(event)">
                <input type="hidden" name="acao" value="locar">
                <input type="hidden" name="idTerno" id="modalIdTerno" value="">

                <div class="modal-info" id="modalInfoTerno"></div>

                <label for="modalTamanho">Tamanho:</label>
                <select name="tamanho" id="modalTamanho" required>
                    <option value="">Selecione um tamanho</option>
                    <option value="PP">PP</option>
                    <option value="P">P</option>
                    <option value="M">M</option>
                    <option value="G">G</option>
                    <option value="GG">GG</option>
                    <option value="XG">XG</option>
                    <option value="XGG">XGG</option>
                </select>

                <label for="modalDias">Quantidade de dias:</label>
                <input type="number" name="dias" id="modalDias" min="1" value="1" required onchange="atualizarPrecoModal()">

                <div class="modal-precio">
                    Valor total: R$ <span id="modalValorTotal">0,00</span>
                </div>

                <input type="submit" value="Confirmar Locação">
            </form>
        </div>
    </div>

    <script>
        function abrirModalLocacao(idTerno, nomeTerno, valorLocacao) {
            document.getElementById('modalIdTerno').value = idTerno;
            document.getElementById('modalValorTotal').textContent = '0,00';
            document.getElementById('modalTamanho').value = '';
            document.getElementById('modalDias').value = '1';
            
            // Exibir informações do terno
            const infoHtml = `<strong>Terno:</strong> ${nomeTerno} | <strong>Valor/dia:</strong> R$ ${valorLocacao.toFixed(2).replace('.', ',')}`;
            document.getElementById('modalInfoTerno').innerHTML = infoHtml;
            
            document.getElementById('modalLocacao').style.display = 'block';
        }

        function fecharModalLocacao() {
            document.getElementById('modalLocacao').style.display = 'none';
        }

        function atualizarPrecoModal() {
            const tamanho = document.getElementById('modalTamanho').value;
            const dias = parseInt(document.getElementById('modalDias').value) || 1;
            const span = document.getElementById('modalValorTotal');
            const infoText = document.getElementById('modalInfoTerno').innerHTML;
            const valorMatch = infoText.match(/R\$ ([\d.,]+)/);
            
            if(tamanho && dias > 0 && valorMatch) {
                const valorStr = valorMatch[1].replace(',', '.');
                const valorDia = parseFloat(valorStr);
                const total = dias * valorDia;
                span.textContent = total.toFixed(2).replace('.', ',');
            } else {
                span.textContent = '0,00';
            }
        }

        function salvarLocacao(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            fetch('../locacao/processar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    alert('✔ Locação realizada com sucesso!');
                    fecharModalLocacao();
                    location.reload();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a locação');
            });
        }

        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            const modal = document.getElementById('modalLocacao');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
