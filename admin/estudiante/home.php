<?php
session_start();
require '../../database.php';

// Verificar si el usuario está logueado y es estudiante
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'estudiante') {
    header("Location: ../../index.php");
    exit;
}

$estudiante_id = $_SESSION['user']['id'];
?>

<?php
// Consultar las asignaturas del estudiante con sus notas y periodos
$sql = "
    SELECT 
        a.name AS asignatura,
        p.name AS periodo,
        n.valor AS nota,
        n.id AS nota_id
    FROM asignaturas_estudiantes ae
    JOIN asignaturas a ON ae.asignatura_id = a.id
    JOIN periodos p ON p.asignatura_id = a.id
    JOIN notas n ON n.periodo_id = p.id
    WHERE ae.estudiante_id = (
        SELECT e.id FROM estudiantes e WHERE e.user_id = :user_id
    )
";

$statement = $conn->prepare($sql);
$statement->bindParam(':user_id', $estudiante_id);
$statement->execute();

$notas = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <h2 class="text-center mb-4">Mis Notas</h2>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Asignatura</th>
                <th>Periodo</th>
                <th>Nota</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notas as $nota): ?>
                <tr>
                    <td><?= htmlspecialchars($nota['asignatura']) ?></td>
                    <td><?= htmlspecialchars($nota['periodo']) ?></td>
                    <td><?= htmlspecialchars($nota['nota']) ?></td>
                    <td>
                        <a href="queja.php?nota_id=<?= $nota['nota_id'] ?>" class="btn btn-warning btn-sm">Enviar Queja</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<a href="ver_notas.php" class="btn btn-info">Ver Notas</a>
<a href="editar_notas.php?asignatura_id=<?= $asignatura_id ?>&estudiante_id=<?= $estudiante['id'] ?>" class="btn btn-sm btn-warning">Editar Notas</a>

<?php require '../../partials/footer.php'; ?>
