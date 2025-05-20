<?php

require 'database.php';

$error = null;

//identificar si el metodo que se está utilizando es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // verificamos que los campos no se manden vacios
    if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['name']) || empty($_POST['cedula']) || empty($_POST['telefono'])) {
        $error = "Por favor, rellene todos los campos";
    // verificamos que el email sea válido
    } else if (!str_contains($_POST['email'], '@')) {
        $error = "El email no es válido";
    } else {
        //verificamos que el email no se repita
        $statement = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
        $statement->bindParam(':email', $_POST['email']);
        $statement->execute();
        //comprobamos que el usuario no exista
        if ($statement->rowCount() > 0) {
            $error = "El email ya existe";
        } else {
            // definimos el rol como estudiante (0)
            $rol = 0;

            // insertamos el nuevo usuario con su rol
            $statement = $conn->prepare("INSERT INTO usuarios(email, password, rol) VALUES(:email, :password, :rol)");
            $statement->execute([
                ":email" => $_POST['email'],
                ":password" => password_hash($_POST['password'], PASSWORD_BCRYPT),
                ":rol" => $rol
            ]);

            // iniciamos sesion con el usuario
            // verificamos que el usuario exista
            $statement = $conn->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
            $statement->bindParam(':email', $_POST['email']);
            $statement->execute();
            //comprobamos que el usuario exista
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            // crear el estudiante con el id del usuario
            $statement = $conn->prepare("INSERT INTO estudiantes(name, cedula, telefono, user_id) VALUES(:name, :cedula, :telefono, :user_id)");
            $statement->bindParam(':name', $_POST['name']);
            $statement->bindParam(':cedula', $_POST['cedula']);
            $statement->bindParam(':telefono', $_POST['telefono']);
            $statement->bindParam(':user_id', $user['id']);
            $statement->execute();

            // desahacer de la contrasena
            unset($user['password']);
            // determinar si el usuario es profesor o estudiante
            if ($user['rol'] == 1) {
                $user['type'] = "profesor";
            } else {
                $user['type'] = "estudiante";
            }
            // iniciamos sesion con el usuario
            $_SESSION['user'] = $user;
            // redireccionamos a la pagina de inicio dependiendo del tipo de usuario
            if ($user['type'] == "profesor") {
                header("Location: ./admin/profesor/home.php");
            } else {
                header("Location: ./admin/estudiante/home.php");
            }
        }
    }
}
?>

<?php require("./partials/header.php"); ?>
<?php require("./partials/navbar.php"); ?>

<div class="container pt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card text-center">
        <div class="card-header">Register</div>
        <div class="card-body">
          <!-- si hay un error mandar un danger -->
          <?php if ($error): ?> 
            <p class="text-danger">
              <?= $error ?>
            </p>
          <?php endif ?>
          <form method="POST" action="register.php">
            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

              <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" required autocomplete="name" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Cedula</label>

              <div class="col-md-6">
                <input id="cedula" type="text" class="form-control" name="cedula" required autocomplete="name" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Telefono</label>

              <div class="col-md-6">
                <input id="telefono" type="text" class="form-control" name="telefono" required autocomplete="name" autofocus>
              </div>              
            </div>

            <div class="mb-3 row">
              <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

              <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" required autocomplete="email" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

              <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" required autocomplete="password" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require("./partials/footer.php"); ?>

