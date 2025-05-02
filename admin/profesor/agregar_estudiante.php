<?php

session_start();

// Verificar si el usuario está autenticado y es profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../../login.php");
    exit;
}

require '../../database.php';

$profesor_id = $_SESSION['user']['id'];
$error = null;

// Obtener el id de la asignatura
$asignatura_id = $_GET['asignatura_id'];

// Obtener la asignatura
$asignatura = $conn->query("SELECT * FROM asignaturas WHERE id = $asignatura_id")->fetch(PDO::FETCH_ASSOC);

// Obtener estudiantes asignados a esta materia
$estudiantesAsignados = $conn->query("
    SELECT e.* 
    FROM estudiantes e
    INNER JOIN asignaturas_estudiantes ae ON e.id = ae.estudiante_id
    WHERE ae.asignatura_id = $asignatura_id
");

// Obtener estudiantes NO asignados a esta materia
$estudiantesNoAsignados = $conn->query("
    SELECT * FROM estudiantes
    WHERE id NOT IN (
        SELECT estudiante_id FROM asignaturas_estudiantes WHERE asignatura_id = $asignatura_id
    )
");

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['estudiantes'])) {
    $seleccionados = $_POST['estudiantes']; // Array de IDs

    foreach ($seleccionados as $estudiante_id) {
        $stmt = $conn->prepare("INSERT INTO asignaturas_estudiantes (asignatura_id, estudiante_id) VALUES (?, ?)");
        $stmt->execute([$asignatura_id, $estudiante_id]);
    }

    // Recargar la página para mostrar cambios
    header("Location: agregar_estudiante.php?asignatura_id=$asignatura_id");
    exit;
}
?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card text-center">
                <div class="card-header">
                    <h4>Estudiantes - <?= htmlspecialchars($asignatura['name']) ?></h4>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#asignados" role="tab" aria-selected="true">Asignados</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#no-asignados" role="tab" aria-selected="false">No Asignados</a>
                    </li>
                </ul>

                <!-- Contenido de Tabs -->
                <div class="tab-content p-3">

                    <!-- Tab: Estudiantes Asignados -->
                    <div class="tab-pane fade show active" id="asignados" role="tabpanel">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cédula</th>
                                    <th>Teléfono</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($estudiantesAsignados->rowCount() > 0): ?>
                                    <?php foreach ($estudiantesAsignados as $estudiante): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($estudiante['name']) ?></td>
                                            <td><?= $estudiante['cedula'] ?></td>
                                            <td><?= $estudiante['telefono'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No hay estudiantes asignados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tab: Estudiantes No Asignados -->
                    <div class="tab-pane fade" id="no-asignados" role="tabpanel">
                        <form method="POST" action="">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Nombre</th>
                                        <th>Cédula</th>
                                        <th>Teléfono</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($estudiantesNoAsignados->rowCount() > 0): ?>
                                        <?php foreach ($estudiantesNoAsignados as $estudiante): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="estudiantes[]" value="<?= $estudiante['id'] ?>">
                                                </td>
                                                <td><?= htmlspecialchars($estudiante['name']) ?></td>
                                                <td><?= $estudiante['cedula'] ?></td>
                                                <td><?= $estudiante['telefono'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No hay estudiantes disponibles.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <button type="submit" class="btn btn-primary mt-3">Asignar Estudiantes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../../partials/footer.php'; ?>