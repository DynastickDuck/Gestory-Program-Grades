<?php 
session_start();

// Verificar si el usuario está autenticado y es profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../../login.php");
    exit;
}

require '../../database.php';

$profesor_id = $_SESSION['user']['id'];

// 1. Obtener las asignaturas del profesor
$asignaturas = $conn->query("SELECT * FROM asignaturas WHERE profesor_id = $profesor_id");

// 2. Obtener todos los estudiantes (sin asignar)
$estudiantes_sin_materia = $conn->query("SELECT e.id, e.name, e.cedula FROM estudiantes e LEFT JOIN asignaturas_estudiantes ae ON e.id = ae.estudiante_id WHERE ae.estudiante_id IS NULL");

// 3. Obtener todos los estudiantes
$estudiantes = $conn->query("SELECT * FROM estudiantes");
?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <h2 class="mb-4">Bienvenido Profesor <?= htmlspecialchars($_SESSION['user']['email']) ?></h2>

    <!-- Sección de Asignaturas -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Tus Materias</h3>
            <!-- Botón para agregar materia -->
            <a href="agregar_materia.php" class="btn btn-primary">Agregar Materia</a>
            <a href="ver_quejas.php" class="btn btn-warning">Ver Quejas</a>

        </div>
        <div class="card-body">
            <?php if ($asignaturas): ?>
                <ul class="list-group">
                    <?php foreach ($asignaturas as $materia): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($materia['name']) ?>
                            <div class="d-flex justify-content-end">
                                <a href="ver_estudiantes.php?asignatura_id=<?= $materia['id'] ?>" class="btn btn-sm btn-primary">Ver Estudiantes</a>
                                <a href="poner_notas.php?asignatura_id=<?= $materia['id'] ?>" class="btn btn-sm btn-info">Poner Notas</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No tienes materias asignadas aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sección de Estudiantes sin materia -->
    <?php if ($estudiantes_sin_materia): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Estudiantes sin Materia</h3>
            <!-- Botón para agregar materia -->
        </div>
        <div class="card-body">
            <?php if ($estudiantes_sin_materia): ?>
                <ul class="list-group">
                    <?php foreach ($estudiantes_sin_materia as $estudiante): ?>
                        <li class="list-group-item"><?= htmlspecialchars($estudiante['name']) ?> (<?= $estudiante['cedula'] ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay estudiantes sin materia.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <?php endif; ?>

    <!-- Sección de Estudiantes -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Estudiantes TOTALES</h3>
            <!-- Botón para agregar materia -->
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
                <p>No hay estudiantes.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '../../partials/footer.php'; ?>