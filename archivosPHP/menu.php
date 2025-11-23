<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menú – Cafetería UTHH</title>
  <link rel="stylesheet" href="/archivosCSS/menu.css" />
  <link rel="stylesheet" href="/archivosCSS/footer.css" />
</head>
<body>
  <div class="app">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar__left">
        <span class="avatar" aria-hidden="true">👤</span>
        <a class="login-pill" href="/archivosHTML/login.html"><?php echo $_SESSION['usuario']; ?></a> <!-- aqui se muestra el nombre de usaurio-->
      </div>
      <h1 class="title">CAFETERIA UTHH</h1>
      <div class="topbar__right"></div>
    </header>

    <!-- NAV -->
    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="/index.php"><span class="ico">🏠</span> HOME</a>
        <a class="pill" href="/archivosPHP/productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill is-active" href="/archivosPHP/menu.php"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill" href="/archivosPHP/pedidos.php"><span class="ico">🧾</span> PEDIDOS</a>
        <a class="pill" href="/archivosPHP/usuarios.php"><span class="ico">👤</span>REGISTROS</a>
      </div>
    </nav>
    <!-- CONTENIDO -->
    <main class="content">
      <div class="menu-grid">

        <!-- Col 1: Guisados -->
        <section class="category">
          <h3 class="category__title">Guisados</h3>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/2232/vegetables-italian-pizza-restaurant.jpg" alt="Guisado de bistec">
            </div>
            <div class="tile__info">
              <strong>Guisado de bistec</strong>
              <span class="price">$60</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/571017/pexels-photo-571017.jpeg" alt="Guisado de salchicha">
            </div>
            <div class="tile__info">
              <strong>Guisado de salchicha</strong>
              <span class="price">$50</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/958545/pexels-photo-958545.jpeg" alt="Guisado de huevo">
            </div>
            <div class="tile__info">
              <strong>Guisado de huevo</strong>
              <span class="price">$60</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/53821/burrito-mexican-mexican-food-restaurant-53821.jpeg" alt="Guisado de empanizado">
            </div>
            <div class="tile__info">
              <strong>Guisado de empanizado</strong>
              <span class="price">$50</span>
            </div>
          </article>
        </section>

        <!-- Col 2: Tacos -->
        <section class="category">
          <h3 class="category__title">Tacos</h3>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg" alt="Taco de bistec">
            </div>
            <div class="tile__info">
              <strong>Taco de bistec</strong>
              <span class="price">$12</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/533325/pexels-photo-533325.jpeg" alt="Tacos de salchicha">
            </div>
            <div class="tile__info">
              <strong>Tacos de salchicha</strong>
              <span class="price">$10</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg" alt="Taco de chicharrón">
            </div>
            <div class="tile__info">
              <strong>Taco de chicharrón</strong>
              <span class="price">$10</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/1420172/pexels-photo-1420172.jpeg" alt="Taco de huevo">
            </div>
            <div class="tile__info">
              <strong>Taco de huevo</strong>
              <span class="price">$10</span>
            </div>
          </article>
        </section>

        <!-- Col 3: Tortas -->
        <section class="category">
          <h3 class="category__title">Tortas</h3>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg" alt="Torta al pastor">
            </div>
            <div class="tile__info">
              <strong>Torta al pastor</strong>
              <span class="price">$40</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg" alt="Torta de salchicha">
            </div>
            <div class="tile__info">
              <strong>Torta de salchicha</strong>
              <span class="price">$40</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg" alt="Torta de milanesa">
            </div>
            <div class="tile__info">
              <strong>Torta de milanesa</strong>
              <span class="price">$40</span>
            </div>
          </article>

          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg" alt="Torta de cochinita con huevo">
            </div>
            <div class="tile__info">
              <strong>Torta de cochinita con huevo</strong>
              <span class="price">$40</span>
            </div>
          </article>
        </section>

        <!-- Col 4: Hamburguesa / Sandwich / Hot dogs -->
        <section class="category narrow">
          <h3 class="category__title">Hamburguesa</h3>
          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/1639562/pexels-photo-1639562.jpeg" alt="Hamburguesa de carne de res">
            </div>
            <div class="tile__info">
              <strong>Hamburguesa de carne de res</strong>
              <span class="price">$45</span>
            </div>
          </article>

          <h3 class="category__title mt">Sandwich</h3>
          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/1600711/pexels-photo-1600711.jpeg" alt="Sandwich clásico">
            </div>
            <div class="tile__info">
              <strong>Sandwich</strong>
              <span class="price">$15</span>
            </div>
          </article>

          <h3 class="category__title mt">Hot dogs</h3>
          <article class="tile">
            <div class="tile__img">
              <img src="https://images.pexels.com/photos/2232/vegetables-italian-pizza-restaurant.jpg" alt="Hot dogs">
            </div>
            <div class="tile__info">
              <strong>Hot dogs</strong>
              <span class="price">$20</span>
            </div>
          </article>
        </section>

      </div>
    </main>
  </div>
  <footer class="footer">
  <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
  <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>
  <form action="#contacto.html" method="get">
    <button type="submit" class="btn-contacto">Contáctanos</button>
  </form>
</footer>
</body>
</html>
