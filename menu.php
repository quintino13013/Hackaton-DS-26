<?php session_start(); ?>

<nav class="menu">
    <a href="index.php" class="logo">Locação de Ternos</a>
    
    <nav class="menu-links">
      <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'AD'): ?>
        <a href="cadastrar/terno.php">Cadastrar Terno</a>
      <?php endif; ?>
      
        <a href="index.php">Home</a>
        <a href="login/login.php">Login</a>
        <a href="cadastrar/pessoa.php">Cadastro</a>
      
        <form action="back/logout.php" method="POST" style="display: inline; margin: 0;">
            <button type="submit" class="btn-sair">
                Sair
            </button>
        </form>
    </nav>
</nav>