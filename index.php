<?php
require_once 'conex.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locação de terno</title>
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="cadastro.php">Cadastro</a>
    </nav>
    <img src="/imagens/banner.jpg" alt="">
    <h1>Encontre o terno ideal para cada situação</h1>

    <section class="produtos">
            <div class = "modelos">
            <h2>Modelos</h2>
            <?php
                require_once 'mostra/terno.php';
            ?>
            </div>
    </section>
</body>
</html>
<?php
require_once 'mostra/terno.php';
?>