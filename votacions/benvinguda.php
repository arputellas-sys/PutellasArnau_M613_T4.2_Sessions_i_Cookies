<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/inc/protegeix.php';
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

$usuari = $_SESSION['usuari'];
$horaLogin = (int) ($_SESSION['hora_login'] ?? time());
$pregunta = (string) $dades['consulta']['pregunta'];
$tancamentStr = (string) $dades['consulta']['tancament'];

$estaOberta = votacioOberta($tancamentStr);
$tsTancament = strtotime($tancamentStr);
$dataFormatada = $tsTancament !== false ? date('d/m/Y \a \l\e\s H:i', $tsTancament) : $tancamentStr;
$usuariHaVotat = jaHaVotat((string) $usuari['email']);

require_once __DIR__ . '/inc/capcalera.php';
?>

<div class="card shadow-sm border-0 p-4">
    <h1 class="h3 fw-bold mb-2">Hola, <?= htmlspecialchars((string) $usuari['nom']) ?>!</h1>
    <p class="text-muted mb-4">Has iniciat sessió <?= htmlspecialchars(tempsDesDe($horaLogin)) ?>.</p>

    <div class="p-3 rounded border mb-4 <?= $temaActual === 'fosc' ? 'bg-secondary bg-opacity-10' : 'bg-white' ?>">
        <h2 class="h5 fw-semibold mb-2">Consulta actual:</h2>
        <p class="fs-5 mb-2"><?= htmlspecialchars($pregunta) ?></p>
        <?php if ($estaOberta): ?>
            <span class="badge bg-success">Votació oberta (tanca el <?= htmlspecialchars($dataFormatada) ?>)</span>
        <?php else: ?>
            <span class="badge bg-danger">Votació tancada (va tancar el <?= htmlspecialchars($dataFormatada) ?>)</span>
        <?php endif; ?>
    </div>

    <div>
        <?php if ($usuari['rol'] === 'admin'): ?>
            <a href="resultats.php" class="btn btn-primary">Consultar els resultats</a>
        <?php elseif ($usuariHaVotat): ?>
            <p class="fw-medium text-success mb-2">✅ Ja has votat.</p>
            <a href="resultats.php" class="btn btn-outline-primary">Veure els resultats</a>
        <?php elseif ($estaOberta): ?>
            <a href="votacio.php" class="btn btn-success btn-lg fw-semibold">Votar ara</a>
        <?php else: ?>
            <a href="resultats.php" class="btn btn-outline-primary">Veure els resultats</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/inc/peu.php'; ?>
