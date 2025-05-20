<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'profesor') {
    header("Location: ../../login.php");
    exit;
}

require '../../database.php';

$asignatura_id = $_GET['asignatura_id'] ?? null;
$estudiante_id = $_GET['estudiante_id'] ?? null;

if (!$asignatura_id || !$estudiante_id) {
    echo "Faltan datos en la URL.";
    exit;
}

// Si se envió una nota nueva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periodo_id = $_POST['periodo_id'];
    $nota = $_POST['valor'];

    $stmt = $conn->prepare("INSERT INTO notas (asignatura_id, estudiante_id, periodo_id, valor) VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
    $stmt->execute([$asignatura_id, $estudiante_id, $periodo_id, $nota]);
}

// Obtener periodos
$periodos = $conn->query("SELECT * FROM periodos");

// Obtener notas actuales
$notas_actuales = $conn->query("
    SELECT p.id AS periodo_id, p.nombre, n.valor
    FROM periodos p
    LEFT JOIN notas n ON n.periodo_id = p.id AND n.asignatura_id = $asignatura_id AND n.estudiante_id = $estudiante_id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <h2>Editar Notas</h2>
    <form method="POST">
        <?php foreach ($notas_actuales as $nota): ?>
            <div class="mb-3">
                <label><?= htmlspecialchars($nota['nombre']) ?></label>
                <input type="hidden" name="periodo_id" value="<?= $nota['periodo_id'] ?>">
                <input type="text" name="valor" value="<?= htmlspecialchars($nota['valor']) ?>" class="form-control" required>
                <button type="submit" class="btn btn-primary mt-1">Guardar</button>
            </div>
        <?php endforeach; ?>
    </form>
</div>

<?php require '../../partials/footer.php'; ?>


