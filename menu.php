
<?php
session_start();

//só entra quem tem o passe, fala ae
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'AD') {
    header("Location: ../index.php");
    exit;
}
?>

<nav class="dropdownmenu">
  <ul>
    <li><a href="#">Cadastrar</a>
      <ul id="submenu">
        <li><a href="../cadastrar/pessoa.php">Curso</a></li>
        <li><a href="../cadastrar/terno.php">Instituicao</a></li>
      </ul>
    </li>
    <li><a href="#">Listar</a>
      <ul id="submenu">
        <li><a href="../lst/curso.php">Modelos</a></li>
        <li><a href="../lst/instiuicao.php">Proprietários</a></li>
        <li><a href="../lst/pessoa.php">Serviços</a></li>
      </ul>
    </li>
    <li><a href="../realiza/servico.php">Realizar Serviço</a></li>
  </ul>
</nav>