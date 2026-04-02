<?php
include("../conex.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nomeTerno = $_POST["nomeTerno"];
    $descricaoterno = $_POST["descricaoTerno"];
    $tipoTerno = $_POST["tipoTerno"]; // now id
    $tipoTecido = $_POST["tipoTecido"]; // now id
    $valorLocacao = $_POST["valorLocacao"];
    $statusTerno = 'A';
    $quantidadeTotal = 0;
    $quantidadeDisponivel = 0;
    $tamanhos = $_POST["tamanhos"] ?? [];
    $quantidades = $_POST["quantidades"] ?? [];

    // Handle image upload as BLOB
    $imagemTerno = null;
    if (isset($_FILES['imagemTerno']) && $_FILES['imagemTerno']['error'] == 0) {
        $imagemTerno = file_get_contents($_FILES['imagemTerno']['tmp_name']);
    }

    $sql = "INSERT INTO ternos 
    (nomeTerno, descricaoTerno, idTipoTerno, idTipoTecido, valorLocacao, quantidadeTotal, quantidadeDisponivel, imagemTerno, statusTerno)
    VALUES (?, ?, ?, ?, ?, 0, 0, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiisss", $nomeTerno, $descricaoterno, $tipoTerno, $tipoTecido, $valorLocacao, $imagemTerno, $statusTerno);

    if ($stmt->execute()) {

        $idTerno = $conn->insert_id;

        // Insert sizes
        foreach ($tamanhos as $idTamanho) {
            $qtd = $quantidades[$idTamanho] ?? 0;
            if ($qtd > 0) {
                $sql2 = "INSERT INTO terno_tamanhos (idTerno, idTamanho, quantidadeDisponivel) VALUES (?, ?, ?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("iii", $idTerno, $idTamanho, $qtd);
                $stmt2->execute();
                $stmt2->close();
            }
        }

        header("Location: ../cadastrar/terno.php");
        exit();

    } else {
        echo "Erro ao cadastrar terno: " . $conn->error;
    }
}
?>

