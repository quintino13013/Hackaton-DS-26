<?php 
$servidor='localhost:3308';
$usuariobanco='root';
$senhabanco='etec123';
$banco='sistema_locadora';
$conn= new mysqli($servidor, $usuariobanco, $senhabanco, $banco);
if ($conn->connect_error){
    die("falha na conexão:". $conn->connect_error);
}
?>