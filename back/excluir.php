<?php
include("../conex.php");

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM ternos WHERE idTerno = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../mostra/terno.php");
        exit();
    } else {
        echo "Erro: " . $conn->error;
    }

} else {
    echo "ID não recebido!";
}
?>