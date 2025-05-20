<?php
session_start();
require '../../database.php';

// verificar que sea estudiante
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'estudiante') {
    header("Location: ../../login.php");
    exit;
}

$estudiante_id = $_SESSION['user']['id'];

// consulta para obtener notas del estudiante con asignatura y periodo
$sql = "SELECT a.name AS asignatura, p.name AS periodo, n.valor AS nota, n.observaciones
FROM notas n
JOIN periodos p ON n.periodo_id = p.id
JOIN asignaturas a ON p.asignatura_id = a.id
JOIN asignaturas_estudiantes ae ON ae.asignatura_id = a.id
JOIN estudiantes e ON ae.estudiante_id = e.id
WHERE e.user_id = ?
ORDER BY a.name, p.name";

$stmt = $conn->prepare($sql);
$stmt->execute([$estudiante_id]);
$notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require("../../partials/header.php"); ?>
<?php require("../../partials/navbar.php"); ?>

<div class="container pt-5">
  <h2 class="mb-4">Mis Notas</h2>
  <table class="table table-bordered table-hover text-center">
      <thead class="table-dark">
          <tr>
              <th>Asignatura</th>
              <th>Periodo</th>
              <th>Nota</th>
              <th>Observaciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach ($notas as $nota): ?>
          <tr>
              <td><?= htmlspecialchars($nota['asignatura']) ?></td>
              <td><?= htmlspecialchars($nota['periodo']) ?></td>
              <td><?= htmlspecialchars($nota['nota']) ?></td>
              <td><?= htmlspecialchars($nota['observaciones']) ?></td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
</div>

<?php require("../../partials/footer.php"); ?>



