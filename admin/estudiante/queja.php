<?php
require '../../database.php';
session_start();

$error = null;
$success = null;

// Obtener ID de la nota desde la URL
if (!isset($_GET['nota_id'])) {
    die('Nota no especificada');
}

$nota_id = $_GET['nota_id'];

// Obtener datos de la nota para mostrar (opcional pero útil)
$statement = $conn->prepare("SELECT notas.id, notas.valor, asignaturas.name AS asignatura, periodos.name AS periodo FROM notas
JOIN periodos ON periodos.id = notas.periodo_id
JOIN asignaturas ON asignaturas.id = periodos.asignatura_id
WHERE notas.id = :nota_id");
$statement->bindParam(':nota_id', $nota_id);
$statement->execute();
$nota = $statement->fetch(PDO::FETCH_ASSOC);

if (!$nota) {
    die('Nota no encontrada');
}

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['comentario'])) {
        $error = "Por favor, escriba su queja.";
    } else {
        $statement = $conn->prepare("INSERT INTO comentarios(comentario, nota_id, user_id) VALUES(:comentario, :nota_id, :user_id)");
        $statement->bindParam(':comentario', $_POST['comentario']);
        $statement->bindParam(':nota_id', $nota_id);
        $statement->bindParam(':user_id', $_SESSION['user']['id']);
        $statement->execute();

        $success = "Queja enviada correctamente.";
    }
}
?>

<?php require '../../partials/header.php'; ?>
<?php require '../../partials/navbar.php'; ?>

<div class="container pt-5">
    <h3>Enviar Queja sobre la Nota</h3>
    <p><strong>Asignatura:</strong> <?= htmlspecialchars($nota['asignatura']) ?></p>
    <p><strong>Periodo:</strong> <?= htmlspecialchars($nota['periodo']) ?></p>
    <p><strong>Nota:</strong> <?= htmlspecialchars($nota['valor']) ?></p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="comentario" class="form-label">Escriba su queja</label>
            <textarea name="comentario" id="comentario" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Queja</button>
    </form>
</div>

<?php require '../../partials/footer.php'; ?>
