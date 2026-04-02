<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//se não é adm, não consegue entrar
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    header("Location: ../index.php");
    exit();
}

//erros ativados, comenta isso aí quando for apresentar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../conex.php';

// Fetch fabric types
$tecidos = $conn->query("SELECT * FROM tipo_tecido ORDER BY nomeTipoTecido");

// Fetch suit types
$tipos_terno = $conn->query("SELECT * FROM tipo_terno ORDER BY nomeTipoTerno");

// Fetch sizes
$tamanhos = $conn->query("SELECT * FROM tamanhos ORDER BY nomeTamanho");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_cadastro'])) {
    require '../back/terno.php';  // chama o processamento
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Terno</title>
<link rel="stylesheet" href="../css/styleCadTerno.css">

</head>
<body>

<h1>Cadastrar Terno</h1>

<form id="formCadastro" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="tipo_cadastro" id="tipo_cadastro" value="terno">

<label for="nomeTerno">Nome do terno:</label>
<input type="text" name="nomeTerno" placeholder="Nome do terno" required>

<label for="valorLocacao">Valor da locação:</label>
<input type="number" name="valorLocacao" min="0" step="0.01" placeholder="0.00" required>

<label for="descricaoTerno">Descrição do terno:</label>
<input type="text" name="descricaoTerno" placeholder="Descrição" required>

<label for="tipoTecido">Selecione tipo do tecido <span style="color:#c00">*</span></label>
    <div class="input-group">
        <select name="tipoTecido" id="tipoTecido" required>
            <option value="">Selecione o tipo de tecido</option>
            <?php 
            $tecidos->data_seek(0);
            while($t = $tecidos->fetch_assoc()): ?>
                <option value="<?= $t['idTipoTecido'] ?>"><?= $t['nomeTipoTecido'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="button" class="btn-popup" onclick="abrirPopup('popupTipoTecido')">+ Cadastrar</button>
    </div><br><br>

<label for="tipoTerno">Selecione tipo do terno <span style="color:#c00">*</span></label>
    <div class="input-group">
        <select name="tipoTerno" id="tipoTerno" required>
            <option value="">Selecione o tipo de terno</option>
            <?php 
            $tipos_terno->data_seek(0);
            while($t = $tipos_terno->fetch_assoc()): ?>
                <option value="<?= $t['idTipoTerno'] ?>"><?= $t['nomeTipoTerno'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="button" class="btn-popup" onclick="abrirPopup('popupTipoTerno')">+ Cadastrar</button>
    </div><br><br>


<label for="tamanhos">Selecione tamanhos disponíveis e quantidades <span style="color:#c00">*</span> <button type="button" class="btn-popup-inline" onclick="abrirPopup('popupTamanho')">+ Cadastrar tamanho</button></label>
<div class="tamanhos-container">
    <?php 
    $tamanhos->data_seek(0);
    while($t = $tamanhos->fetch_assoc()): ?>
        <div class="tamanho-item">
            <input type="checkbox" name="tamanhos[]" value="<?= $t['idTamanho'] ?>" id="tamanho_<?= $t['idTamanho'] ?>">
            <label for="tamanho_<?= $t['idTamanho'] ?>"><?= $t['nomeTamanho'] ?> - Qtd:</label>
            <input type="number" name="quantidades[<?= $t['idTamanho'] ?>]" min="0" value="0">
        </div>
    <?php endwhile; ?>
</div><br><br>


<label for="imagemTerno">Imagem do terno:</label>
<input type="file" name="imagemTerno" accept="image/*">

<br><br>
<button type="submit">Cadastrar Terno</button>
<a href="../index.php">Home</a>


</form>

<!-- POPUPS MODAIS -->
<div id="popupTipoTecido" class="popup-modal" onclick="fecharPopup(event, 'popupTipoTecido')">
    <div class="popup-content" onclick="event.stopPropagation()">
        <div class="popup-header">
            <h2>Cadastrar Tipo de Tecido</h2>
            <button type="button" class="popup-close" onclick="fecharPopup(null, 'popupTipoTecido')">&times;</button>
        </div>
        <div class="popup-body" id="popupTipoTecidoContent"></div>
    </div>
</div>

<div id="popupTipoTerno" class="popup-modal" onclick="fecharPopup(event, 'popupTipoTerno')">
    <div class="popup-content" onclick="event.stopPropagation()">
        <div class="popup-header">
            <h2>Cadastrar Tipo de Terno</h2>
            <button type="button" class="popup-close" onclick="fecharPopup(null, 'popupTipoTerno')">&times;</button>
        </div>
        <div class="popup-body" id="popupTipoTernoContent"></div>
    </div>
</div>

<div id="popupTamanho" class="popup-modal" onclick="fecharPopup(event, 'popupTamanho')">
    <div class="popup-content" onclick="event.stopPropagation()">
        <div class="popup-header">
            <h2>Cadastrar Tamanho</h2>
            <button type="button" class="popup-close" onclick="fecharPopup(null, 'popupTamanho')">&times;</button>
        </div>
        <div class="popup-body" id="popupTamanhoContent"></div>
    </div>
</div>

<link rel="stylesheet" href="../css/stylePopUp.css">

<script>
    function abrirPopup(popupId) {
        const popup = document.getElementById(popupId);
        const contentDiv = document.getElementById(popupId + 'Content');

        // Determinar qual arquivo carregar
        let arquivo = '';
        if (popupId === 'popupTipoTecido') {
            arquivo = '../cadastrar/tipo_tecido.php';
        } else if (popupId === 'popupTipoTerno') {
            arquivo = '../cadastrar/tipo_terno.php';
        } else if (popupId === 'popupTamanho') {
            arquivo = '../cadastrar/tamanho.php';
        }

        // Carregar conteúdo via AJAX
        fetch(arquivo)
            .then(response => response.text())
            .then(html => {
                // Extrair apenas o body do HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const bodyContent = doc.body.innerHTML;
                contentDiv.innerHTML = bodyContent;

                // Reaplicar event listener ao novo formulário
                const form = contentDiv.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        enviarFormularioPopup(e, popupId);
                    });
                }

                // Mostrar popup
                popup.classList.add('show');
            })
            .catch(error => console.error('Erro ao carregar:', error));
    }

    function fecharPopup(event, popupId) {
        // Se clicou no X ou fora do modal
        if (event === null || event.target.id === popupId) {
            document.getElementById(popupId).classList.remove('show');
        }
    }

    function enviarFormularioPopup(e, popupId) {
        const form = e.target;
        const formData = new FormData(form);

        let apiUrl = '';
        if (popupId === 'popupTipoTecido') {
            apiUrl = '../back/tipo_tecido.php';
        } else if (popupId === 'popupTipoTerno') {
            apiUrl = '../back/tipo_terno.php';
        } else if (popupId === 'popupTamanho') {
            apiUrl = '../back/tamanho.php';
        }

        fetch(apiUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                // Sucesso - mostrar mensagem e recarregar dados
                alert(data.mensagem);
                
                if (popupId === 'popupTipoTecido' || popupId === 'popupTipoTerno') {
                    location.reload();
                } else if (popupId === 'popupTamanho') {
                    location.reload();
                }
            } else {
                // Erro - mostrar mensagem de erro
                alert('Erro: ' + data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro ao enviar:', error);
            alert('Erro ao enviar dados: ' + error);
        });
    }
</script>
</body>
</html>