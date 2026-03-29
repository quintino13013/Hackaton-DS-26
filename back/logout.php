<?php

session_start();

//deleta todas as informações de sesão q foram salvas
$_SESSION = array();

//destroi os cookies pra garantir
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

//quebra a sessão
session_destroy();

header("Location: ../index.php");
exit();
?>