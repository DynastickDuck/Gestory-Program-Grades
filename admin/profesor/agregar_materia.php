<?php

session_start();

// Verificar si el usuario está autenticado y es profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../login.php");
    exit;
}

require '../../database.php';

$profesor_id = $_SESSION['user']['id'];

$error = null;

//identificar si el metodo que se está utilizando es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // verificamos que los campos no se manden vacios
    if (empty($_POST['name']) || empty($_POST['description']) || empty($_POST['numero_periodos']) || empty($_POST['numero_notas'])) {
        $error = "Por favor, rellene todos los campos";
    } else {

        // Insertar en asignaturas
        $stmtAsignatura = $conn->prepare("INSERT INTO asignaturas(name, description, profesor_id) VALUES(:name, :description, :profesor_id)");
        $stmtAsignatura->execute([
            ":name" => $_POST['name'],
            ":description" => $_POST['description'],
            ":profesor_id" => $profesor_id,
        ]);

        $asignatura_id = $conn->lastInsertId(); // Obtener el ID recién creado

        // Insertar los periodos
        $stmtPeriodo = $conn->prepare("INSERT INTO periodos(name, cantidad_notas, asignatura_id) VALUES(:name, :cantidad_notas, :asignatura_id)");

        for ($i = 0; $i < $_POST['numero_periodos']; $i++) {
            $params = [
                ":name" => "Periodo " . ($i + 1),
                ":cantidad_notas" => $_POST['numero_notas'],
                ":asignatura_id" => $asignatura_id, // ✅ Usamos el ID ya guardado
            ];
            $stmtPeriodo->execute($params);
        }

        header("Location: ../profesor/home.php");
    }
}

?>


<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-header">Agregar Materia</div>
                <div class="card-body">
                    <!-- si hay un error mandar un danger -->
                    <?php if ($error): ?>
                        <p class="text-danger">
                            <?= $error ?>
                        </p>
                    <?php endif ?>
                    <form method="POST" action="agregar_materia.php">
                        <div class="mb-3 row">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="description" class="col-md-4 col-form-label text-md-end">Descripción</label>

                            <div class="col-md-6">
                                <textarea id="description" class="form-control" name="description" required></textarea>
                            </div>
                        </div>

                        <!-- numero de periodos -->
                        <div class="mb-3 row">
                            <label for="numero_periodos" class="col-md-4 col-form-label text-md-end">Numero de Periodos</label>

                            <div class="col-md-6">
                                <input id="numero_periodos" type="number" class="form-control" name="numero_periodos" required>
                            </div>
                        </div>

                        <!-- numero de notas por periodo -->
                        <div class="mb-3 row">
                            <label for="numero_notas" class="col-md-4 col-form-label text-md-end">Numero de Notas</label>

                            <div class="col-md-6">
                                <input id="numero_notas" type="number" class="form-control" name="numero_notas" required>
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
<?php require '../../partials/footer.php'; ?>