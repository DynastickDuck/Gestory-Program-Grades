<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <!-- Logo y nombre -->
    <?php if (isset($_SESSION["user"])) : ?>
      <a class="navbar-brand d-flex align-items-center" href="home.php">
        <img src="/Gestory-Program-Grades/Assets/img/logo.png" alt="Logo" width="30" height="30" class="me-2">
        Gestory Program Grades
      </a>
    <?php else : ?>
      <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="/Gestory-Program-Grades/Assets/img/logo.png" alt="Logo" width="30" height="30" class="me-2">
        Gestory Program Grades
      </a>
    <?php endif; ?>

    <!-- Botón responsive -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menú colapsable -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION["user"])) : ?>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="home.php">Home</a>
          </li>
        <?php else : ?>
          <li class="nav-item">
            <a class="nav-link active" href="register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Mostrar email del usuario si está logueado -->
      <?php if (isset($_SESSION["user"])): ?>
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION["user"]["email"]) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item text-danger" href="/Gestory-Program-Grades/logout.php">Cerrar sesión</a></li>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container">