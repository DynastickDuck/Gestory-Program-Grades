<?php 
session_start();

// Verificar si el usuario está autenticado y es profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../login.php");
    exit;
}

require '../../database.php';

$profesor_id = $_SESSION['user']['id'];

// 1. Obtener las asignaturas del profesor
$stmt_asignaturas = $conn->prepare("
    SELECT * FROM asignaturas 
    WHERE profesor_id = :profesor_id
");
$stmt_asignaturas->bindParam(':profesor_id', $profedor_id);
$stmt_asignaturas->execute();
$asignaturas = $stmt_asignaturas->fetchAll(PDO::FETCH_ASSOC);

// 2. Obtener todos los estudiantes (sin asignar)
$stmt_estudiantes = $conn->prepare("
    SELECT e.id, e.name, e.cedula 
    FROM estudiantes e
    LEFT JOIN asignaturas_estudiantes ae ON e.id = ae.estudiante_id
    WHERE ae.estudiante_id IS NULL
");
$stmt_estudiantes->execute();
$estudiantes_sin_materia = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <h2 class="mb-4">Bienvenido Profesor <?= htmlspecialchars($_SESSION['user']['email']) ?></h2>

    <!-- Sección de Asignaturas -->
    <div class="card mb-4">
        <div class="card-header">Tus Materias</div>
        <div class="card-body">
            <?php if ($asignaturas): ?>
                <ul class="list-group">
                    <?php foreach ($asignaturas as $materia): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($materia['name']) ?>
                            <a href="ver_estudiantes.php?asignatura_id=<?= $materia['id'] ?>" class="btn btn-sm btn-primary">Ver Estudiantes</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No tienes materias asignadas aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sección de Estudiantes sin materia -->
    <div class="card mb-4">
        <div class="card-header">Estudiantes sin Materia</div>
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
</div>

<?php require '../../partials/footer.php'; ?>