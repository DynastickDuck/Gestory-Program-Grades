<?php

require 'database.php';

$error = null;

//identificar si el metodo que se está utilizando es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // verificamos que los campos no se manden vacios
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Por favor, rellene todos los campos";
    // verificamos que el email sea válido
    } else if (!str_contains($_POST['email'], '@')) {
        $error = "El email no es válido";
    } else {
        //verificamos que el email exista en la base de datos
        $statement = $conn->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $statement->bindParam(':email', $_POST['email']);
        $statement->execute();
        //comprobamos que el usuario exista
        if ($statement->rowCount() === 0) {
            $error = "El email no existe";
        } else {
            //obtener datos del usuario y transformarlos a un array asociativo para su futuro uso
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            //verificamos que la contraseña sea correcta
            if (!password_verify($_POST['password'], $user['password'])) {
                $error = "La contraseña es incorrecta";
            } else {
                // desahacer de la contrasena
                unset($user['password']);
                // iniciamos una sesion la cual es como una cookie
                session_start();
                // guardamos el usuario en la sesion
                $_SESSION['user'] = $user;
                // redireccionamos a la pagina de inicio
                header("Location: home.php");
            }
        }
    }
}

?>

<?php require 'partials/header.php'; ?>
<?php require 'partials/nabvar.php'; ?>

    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <!-- mostrar errores si es que existen -->
                        <?php if(isset($error)): ?>
                            <p class="text-danger">
                                <?= $error ?>
                            </p>
                        <?php endif; ?>

                        <form action="login.php" method="post">
                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>
                                <div class="col-mb-6">
                                    <input type="email" id="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            <div>
                                <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>
                                <div class="col-mb-6">
                                    <input type="password" id="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <p>Don't have acount yet?</p>
                            <a href="register.php" style="color: #ff9900;">Register!</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require 'partials/footer.php'; ?>