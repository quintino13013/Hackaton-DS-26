<?php 

//erros ativados, comenta isso aí quando for apresentar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

<label for="quantidadeTotal">Quantidade Total:</label>
<input type="number" name="quantidadeTotal" id="quantidadeTotal" min="1" max="9999" step="1"><br><br>

<label for="tipoTecido">Selecione tipo do tecido <span style="color:#c00">*</span></label>
    <select name="tipoTecido" id="tipoTecido" required>
        <option value="">Selecione o tipo de tecido</option>
        <option value="Lã fria">Lã fria</option>
        <option value="Linho">Linho</option>
        <option value="Algodão">Algodão</option>
        <option value="Poliéster/Misturas">Poliéster/Misturas</option>
    </select><br><br>

<label for="tipoTerno">Selecione tipo do terno <span style="color:#c00">*</span></label>
    <select name="tipoTerno" id="tipoTerno" required>
        <option value="">Selecione o tipo de terno</option>
        <option value="Terno">Terno</option>
        <option value="Costume">Costume</option>
        <option value="Smoking">Smoking</option>
        <option value="Blazer">Blazer</option>
        <option value="Fraque">Fraque</option>
    </select>

<<label for="imagemTerno">Imagem do terno:</label>
<input type="file" name="imagemTerno" accept="image/*">

<br><br>
<button type="submit">Cadastrar Terno</button>



</form>

</body>
</html>