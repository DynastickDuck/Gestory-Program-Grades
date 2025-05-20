<?php
session_start();
require '../../database.php';

// verificar que sea profesor
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../../login.php");
    exit;
}

// obtener asignaturas del profesor
$profesor_id = $_SESSION['user']['id'];
$asignaturas = $conn->query("SELECT * FROM asignaturas WHERE profesor_id = $profesor_id")->fetchAll(PDO::FETCH_ASSOC);

// guardar nota si se envió el formulario
$mensaje = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'];
    $periodo_id = $_POST['periodo_id'];
    $nota = $_POST['nota'];
    $observaciones = $_POST['observaciones'];

    // insertar nota
    $stmt = $conn->prepare("INSERT INTO notas (estudiante_id, periodo_id, valor, observaciones) VALUES (?, ?, ?, ?)");
    $stmt->execute([$estudiante_id, $periodo_id, $nota, $observaciones]);
    $mensaje = "Nota registrada correctamente";
}
?>

<?php require("../../partials/header.php"); ?>
<?php require("../../partials/navbar.php"); ?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-10">

      <div class="card">
        <div class="card-header bg-primary text-white">
          Poner Notas
        </div>
        <div class="card-body">
          <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label for="estudiante_id" class="form-label">Estudiante</label>
              <select name="estudiante_id" class="form-select" required>
                <option value="">Seleccione un estudiante</option>
                <?php
                $estudiantes = $conn->query("SELECT * FROM estudiantes")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($estudiantes as $e):
                ?>
                  <option value="<?= $e['id'] ?>"><?= $e['name'] ?> (<?= $e['cedula'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="periodo_id" class="form-label">Periodo</label>
              <select name="periodo_id" class="form-select" required>
                <option value="">Seleccione un periodo</option>
                <?php
                $periodos = $conn->query("SELECT p.id, p.name, a.name AS asignatura FROM periodos p JOIN asignaturas a ON p.asignatura_id = a.id WHERE a.profesor_id = $profesor_id")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($periodos as $p):
                ?>
                  <option value="<?= $p['id'] ?>"><?= $p['asignatura'] ?> - <?= $p['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="nota" class="form-label">Nota</label>
              <input type="number" step="0.01" name="nota" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="observaciones" class="form-label">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-success">Guardar Nota</button>
              <a href="home.php" class="btn btn-secondary">Volver al Home</a>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require("../../partials/footer.php"); ?>

