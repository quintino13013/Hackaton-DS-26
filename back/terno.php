<?php
include("../conex.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nomeTerno = $_POST["nomeTerno"];
    $descricaoterno = $_POST["descricaoTerno"];
    $tipoTerno = $_POST["tipoTerno"];
    $tipoTecido = $_POST["tipoTecido"];
    $valorLocacao = $_POST["valorLocacao"];
    $quantidadeTotal = $_POST["quantidadeTotal"];
    $quantidadeDisponivel = $quantidadeTotal;

    // Handle image upload as BLOB
    $imagemTerno = null;
    if (isset($_FILES['imagemTerno']) && $_FILES['imagemTerno']['error'] == 0) {
        $imagemTerno = file_get_contents($_FILES['imagemTerno']['tmp_name']);
    }

    $sql = "INSERT INTO ternos 
    (nomeTerno, descricaoTerno, tipoTerno, tipoTecido, valorLocacao, quantidadeTotal, quantidadeDisponivel, imagemTerno)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssiis", $nomeTerno, $descricaoterno, $tipoTerno, $tipoTecido, $valorLocacao, $quantidadeTotal, $quantidadeDisponivel, $imagemTerno);

    if ($stmt->execute()) {

        $idTerno = $conn->insert_id;

        header("Location: ../mostra/terno.php");
        exit();

    } else {
        echo "Erro ao cadastrar terno: " . $conn->error;
    }
}
?>

