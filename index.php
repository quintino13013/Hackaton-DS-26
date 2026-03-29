<?php
require_once 'conex.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locação de terno</title>
    <link rel="stylesheet" href="css/styleIndex.css">
</head>
<script>
    const slides = document.querySelectorAll('#carousel li');
    let current = 0;

    function nextSlide() {
        slides[current].classList.remove('active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('active');
    }

    setInterval(nextSlide, 5000);
</script>
<body>
    <!-- Menu -->
<nav class="menu">
    <a href="index.php" class="logo">Locação de Ternos</a>
    <nav>
        <a href="index.php">Home</a>
        <a href="back/login.php">Login</a>
        <a href="cadastrar/pessoa.php">Cadastro</a>
    </nav>
</nav>

<!-- Hero com carrossel -->
<div class="hero">
    <ul class="carrossel" id="carousel">
        <li class="active"><img src="/imagens/banner.jpeg" alt="Terno elegante"></li>
        <li><img src="/imagens/banner2.jpg" alt="Terno moderno"></li>
        <li><img src="/imagens/banner3.jpg" alt="Terno clássico"></li>
    </ul>
    
    <div class="hero-content">
        <h1>Encontre o terno ideal<br>para cada ocasião</h1>
        <p>Qualidade premium • Ajuste perfeito • Entrega rápida</p>
        <a href="#produtos" class="btn">Ver Modelos</a>
    </div>
</div>

<!-- Seção de produtos -->
<section class="produtos" id="produtos">
    <div class="container">
        <h2>Nossos Modelos</h2>
        <div class="modelos">
            <?php require_once 'mostra/terno.php'; ?>
        </div>
    </div>
</section>
</body>
</html>
<?php
require_once 'mostra/terno.php';
?>
