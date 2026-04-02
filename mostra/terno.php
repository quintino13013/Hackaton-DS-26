<?php

include("conex.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Show detail
    $sql = "SELECT t.*, tt.nomeTipoTerno, tec.nomeTipoTecido 
            FROM ternos t 
            LEFT JOIN tipo_terno tt ON t.idTipoTerno = tt.idTipoTerno 
            LEFT JOIN tipo_tecido tec ON t.idTipoTecido = tec.idTipoTecido 
            WHERE t.idTerno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $terno = $result->fetch_assoc();
    $stmt->close();

    if (!$terno) {
        echo "Terno não encontrado.";
        exit;
    }

    // Get sizes
    $sql_sizes = "SELECT tam.nomeTamanho, ttam.quantidadeDisponivel 
                  FROM terno_tamanhos ttam 
                  JOIN tamanhos tam ON ttam.idTamanho = tam.idTamanho 
                  WHERE ttam.idTerno = ?";
    $stmt_sizes = $conn->prepare($sql_sizes);
    $stmt_sizes->bind_param("i", $id);
    $stmt_sizes->execute();
    $sizes = $stmt_sizes->get_result();
    $stmt_sizes->close();

} else {
    // List all
    $sql = "SELECT t.*, tt.nomeTipoTerno, tec.nomeTipoTecido 
            FROM ternos t 
            LEFT JOIN tipo_terno tt ON t.idTipoTerno = tt.idTipoTerno 
            LEFT JOIN tipo_tecido tec ON t.idTipoTecido = tec.idTipoTecido";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Detalhe do Terno' : 'Nossos Ternos'; ?></title>
    <link rel="stylesheet" href="../css/styleMostra.css">
    <link rel="stylesheet" href="../css/styleCardLocar.css">
</head>
<body>

    <div class="container">
        <?php if ($id): ?>
            <!-- Detail view -->
            <h1><?php echo htmlspecialchars($terno['nomeTerno']); ?></h1>
            <div class="terno-detail">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($terno['imagemTerno']); ?>" 
                     alt="<?php echo htmlspecialchars($terno['nomeTerno']); ?>" style="max-width:300px;">

                <div class="detail-info">
                    <p><strong>Tipo:</strong> <?php echo htmlspecialchars($terno['nomeTipoTerno']); ?></p>
                    <p><strong>Tecido:</strong> <?php echo htmlspecialchars($terno['nomeTipoTecido']); ?></p>
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($terno['descricaoTerno']); ?></p>
                    <p><strong>Valor da Locação:</strong> R$ <?php echo number_format($terno['valorLocacao'], 2, ',', '.'); ?>/dia</p>
                    <p><strong>Tamanhos Disponíveis:</strong></p>
                    <ul>
                        <?php while($size = $sizes->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($size['nomeTamanho']); ?> - <?php echo $size['quantidadeDisponivel']; ?> unidades</li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <a href="terno.php">Voltar à lista</a>
        <?php else: ?>
            <!-- List view -->
            <h1>Nossos Ternos para Locação</h1>

            <div class="ternos-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Calculate total available
                        $sql_total = "SELECT SUM(quantidadeDisponivel) as total FROM terno_tamanhos WHERE idTerno = ?";
                        $stmt_total = $conn->prepare($sql_total);
                        $stmt_total->bind_param("i", $row['idTerno']);
                        $stmt_total->execute();
                        $total_result = $stmt_total->get_result();
                        $total_row = $total_result->fetch_assoc();
                        $quantidadeDisponivel = $total_row['total'] ?? 0;
                        $stmt_total->close();

                        $descricao = !empty($row['descricaoTerno']) ? substr($row['descricaoTerno'], 0, 120) . '...' : 'Sem descrição disponível.';
                        $disponivel = $quantidadeDisponivel > 0 ? 'disponivel' : 'esgotado';
                        $status = $quantidadeDisponivel > 0 ? 'Disponível' : 'Esgotado';
                ?>
                
                <div class="terno-card">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagemTerno']); ?>" 
                         alt="<?php echo htmlspecialchars($row['nomeTerno']); ?>">

                    <div class="card-body">
                        <h2><?php echo htmlspecialchars($row['nomeTerno']); ?></h2>
                        
                        <p class="terno-info"><strong>Tipo:</strong> <?php echo htmlspecialchars($row['nomeTipoTerno']); ?></p>
                        <p class="terno-info"><strong>Tecido:</strong> <?php echo htmlspecialchars($row['nomeTipoTecido']); ?></p>
                        
                        <p class="preco">R$ <?php echo number_format($row['valorLocacao'], 2, ',', '.'); ?>/dia</p>
                        
                        <p class="descricao"><?php echo htmlspecialchars($descricao); ?></p>
                        
                        <p class="<?php echo $disponivel; ?>">
                            <?php echo $status; ?> • <?php echo $quantidadeDisponivel; ?> unidades
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
                                   class="btn btn-excluir">Excluir</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p>Nenhum terno cadastrado.</p>";
                }
                ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>


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

                <label for="modalMetodoPagamento">Método de Pagamento:</label>
                <select name="metodoPagamento" id="modalMetodoPagamento" required>
                    <option value="">Selecione o método</option>
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão">Cartão</option>
                    <option value="Pix">Pix</option>
                </select>

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
            document.getElementById('modalDias').value = '1';
            
            // Exibir informações do terno
            const infoHtml = `<strong>Terno:</strong> ${nomeTerno} | <strong>Valor/dia:</strong> R$ ${valorLocacao.toFixed(2).replace('.', ',')}`;
            document.getElementById('modalInfoTerno').innerHTML = infoHtml;
            
            // Carregar tamanhos disponíveis
            fetch(`../locacao/get_sizes.php?idTerno=${idTerno}`)
            .then(response => response.json())
            .then(sizes => {
                const select = document.getElementById('modalTamanho');
                select.innerHTML = '<option value="">Selecione um tamanho</option>';
                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.idTamanho;
                    option.textContent = `${size.nomeTamanho} (${size.quantidadeDisponivel} disponíveis)`;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erro ao carregar tamanhos:', error);
            });
            
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
