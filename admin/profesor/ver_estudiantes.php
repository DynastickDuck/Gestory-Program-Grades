<?php

session_start();

// Verificar si el usuario está autenticado y es profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../../login.php");
    exit;
}

require '../../database.php';

$profesor_id = $_SESSION['user']['id'];

// Obtener el id de la asignatura
$asignatura_id = $_GET['asignatura_id'];

// obtener el nombre de la asignatura
$asignatura = $conn->query("SELECT * FROM asignaturas WHERE id = $asignatura_id")->fetch(PDO::FETCH_ASSOC);

// Obtener todos los estudiantes asignados a la asignatura
$estudiantes = $conn->query("SELECT * FROM estudiantes e LEFT JOIN asignaturas_estudiantes ae ON e.id = ae.estudiante_id WHERE ae.asignatura_id = $asignatura_id");

?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <h2 class="mb-4">Asignatura: <?= htmlspecialchars($asignatura['name']) ?></h2>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Estudiantes Asignados</h3>
            <a href="agregar_estudiante.php?asignatura_id=<?= $asignatura_id ?>" class="btn btn-primary">Agregar Estudiante</a>
        </div>
        <div class="card-body">
            <?php if ($estudiantes): ?>
                <ul class="list-group">
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($estudiante['name']) ?> (<?= $estudiante['cedula'] ?>)
                            <a href="detalle_estudiante.php?id=<?= $estudiante['id'] ?>" class="btn btn-sm btn-primary">Ver Detalle</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay estudiantes asignados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require '../../partials/footer.php'; ?>