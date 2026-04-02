<?php
include("../conex.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//se não é adm, não consegue entrar
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    header("Location: ../index.php");
    exit();
}

$sql = "SELECT 
l.idLocacao, 
p.nomePessoa, 
t.nomeTerno, 
t.valorLocacao,
l.metodoPagamento,
l.dataLocacao, 
l.dataDevolucao, 
l.statusLocacao
FROM locacoes l
JOIN pessoa p ON l.idCliente = p.idPessoa
JOIN ternos t ON l.idTerno = t.idTerno";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Locações</title>
    <link rel="stylesheet" href="../css/styleHistorico.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Histórico de Locações</h2>
        </div>

        <?php 
        // Exibir mensagens de feedback
        if(isset($_GET['sucesso'])){
            $sucesso = $_GET['sucesso'];
            if($sucesso == 'devolucao_realizada'){
                echo '<div class="alert alert-success">✔ Terno devolvido com sucesso!</div>';
            } elseif($sucesso == 'exclusao_realizada'){
                $total = isset($_GET['total']) ? (int)$_GET['total'] : 1;
                echo '<div class="alert alert-success">✔ ' . $total . ' locação(ões) excluída(s) com sucesso!</div>';
            }
        } elseif(isset($_GET['erro'])){
            $erro = $_GET['erro'];
            if($erro == 'locacao_nao_encontrada'){
                echo '<div class="alert alert-error">✗ Locação não encontrada</div>';
            } elseif($erro == 'locacao_ja_devolvida'){
                echo '<div class="alert alert-error">✗ Esta locação já foi devolvida</div>';
            } elseif($erro == 'erro_devolucao'){
                echo '<div class="alert alert-error">✗ Erro ao processar a devolução</div>';
            } elseif($erro == 'nenhuma_selecao'){
                echo '<div class="alert alert-error">✗ Selecione ao menos uma locação para excluir</div>';
            } elseif($erro == 'erro_exclusao'){
                echo '<div class="alert alert-error">✗ Erro ao excluir as locações</div>';
            }
        }
        ?>

        <?php if(mysqli_num_rows($result) > 0){ ?>
            <div class="table-wrapper">
                <form id="formExcluir" method="POST" action="excluir_locacoes.php">
                    <div class="table-actions">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" id="selectAll" onchange="selecionarTodos(this)">
                            <span>Selecionar Todos</span>
                        </label>
                        <button type="submit" class="btn-excluir-selecionados" onclick="return confirm('Tem certeza que deseja excluir as locações selecionadas?');">
                            🗑 Excluir Selecionados
                        </button>
                        <span id="contadorSelecionados" class="contador-selecionados">0 selecionados</span>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 30px;"></th>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Terno</th>
                                <th>Valor/dia</th>
                                <th>Pagamento</th>
                                <th>Data Locação</th>
                                <th>Data Devolução</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)){ ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="locacoes[]" value="<?php echo $row['idLocacao']; ?>" class="checkbox-locacao" onchange="atualizarContador()">
                                    </td>
                                    <td><?php echo $row['idLocacao']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nomePessoa']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nomeTerno']); ?></td>
                                    <td class="valor-locacao">R$ <?php echo number_format($row['valorLocacao'], 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row['metodoPagamento']) ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['dataLocacao'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['dataDevolucao'])); ?></td>
                                    <td>
                                        <?php if($row['statusLocacao'] == 'AL'){ ?>
                                            <span class="status-badge status-ativo">Alugado</span>
                                        <?php } else { ?>
                                            <span class="status-badge status-finalizado">Devolvido</span>
                                        <?php } ?>
                                    </td>
                                    <td class="action-cell">
                                        <?php if($row['statusLocacao'] == 'AL'){ ?>
                                            <a href="devolver.php?id=<?php echo $row['idLocacao']; ?>" 
                                               onclick="return confirm('Tem certeza que deseja devolver este terno?');"
                                               class="btn-devolver">Devolver</a>
                                        <?php } else { ?>
                                            <span class="finalizado">Finalizado</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>
            </div>
        <?php } else { ?>
            <div class="empty-state">
                <p>Nenhuma locação encontrada</p>
            </div>
        <?php } ?>

        <div class="footer-nav">
            <a href="../index.php" class="btn-home">← Voltar para Home</a>
        </div>
    </div>

    <script>
        function selecionarTodos(checkbox) {
            const checkboxes = document.querySelectorAll('.checkbox-locacao');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
            atualizarContador();
        }

        function atualizarContador() {
            const checkboxes = document.querySelectorAll('.checkbox-locacao:checked');
            const contador = document.getElementById('contadorSelecionados');
            contador.textContent = checkboxes.length + ' selecionado' + (checkboxes.length !== 1 ? 's' : '');
            
            // Atualizar estado do botão
            const btnExcluir = document.querySelector('.btn-excluir-selecionados');
            btnExcluir.disabled = checkboxes.length === 0;
        }

        // Inicializar contador na carga da página
        document.addEventListener('DOMContentLoaded', function() {
            atualizarContador();
        });

        // Validar se há seleções antes de excluir
        document.getElementById('formExcluir').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.checkbox-locacao:checked');
            if(checkboxes.length === 0) {
                e.preventDefault();
                alert('Selecione ao menos uma locação para excluir');
            }
        });
    </script>
</body>
</html>