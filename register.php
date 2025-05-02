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
            $statement = $conn->prepare("INSERT INTO usuarios(email, password) VALUES(:email, :password)");
            // sanitizar datos
            $statement->execute([
                ":email" => $_POST['email'],
                ":password" => password_hash($_POST['password'], PASSWORD_BCRYPT),
            ]);

            // iniciamos sesion con el usuario
            // verificamos que el usuario exista
        }
    }
}
        