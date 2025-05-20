<?php
session_start();
require '../../database.php';

// verificar que sea profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../../login.php");
    exit;
}

$profesor_id = $_SESSION['user']['id'];

// obtener las quejas relacionadas a las materias del profesor
$sql = "
    SELECT c.comentario, u.email AS estudiante_email, a.name AS asignatura, n.valor AS nota
    FROM comentarios c
    JOIN notas n ON c.nota_id = n.id
    JOIN periodos p ON n.periodo_id = p.id
    JOIN asignaturas a ON p.asignatura_id = a.id
    JOIN usuarios u ON c.user_id = u.id
    JOIN profesores pr ON a.profesor_id = pr.id
    WHERE pr.user_id = ?
    ORDER BY a.name
";

$stmt = $conn->prepare($sql);
$stmt->execute([$profesor_id]);
$quejas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require("../../partials/header.php"); ?>
<?php require("../../partials/navbar.php"); ?>

<div class="container pt-5">
  <h2 class="mb-4">Quejas Recibidas</h2>
  <?php if (count($quejas) === 0): ?>
    <p class="text-muted">No hay quejas registradas</p>
  <?php else: ?>
    <table class="table table-bordered table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th>Estudiante</th>
                <th>Asignatura</th>
                <th>Nota</th>
                <th>Comentario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quejas as $q): ?>
            <tr>
                <td><?= htmlspecialchars($q['estudiante_email']) ?></td>
                <td><?= htmlspecialchars($q['asignatura']) ?></td>
                <td><?= htmlspecialchars($q['nota']) ?></td>
                <td><?= htmlspecialchars($q['comentario']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require("../../partials/footer.php"); ?>

