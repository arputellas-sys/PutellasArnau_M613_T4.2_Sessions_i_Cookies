<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/inc/protegeix.php';
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

$usuari = $_SESSION['usuari'];
$email = (string) $usuari['email'];
$opcions = $dades['consulta']['opcions'];
$tancament = (string) $dades['consulta']['tancament'];

// 1. Comprovacions obligatòries en l'ordre exacte de l'enunciat:
if ($usuari['rol'] !== 'votant') {
    afegeixMissatge('Només els votants poden votar.');
    header('Location: benvinguda.php');
    exit;
}

if (!votacioOberta($tancament)) {
    afegeixMissatge('La votació està tancada.');
    header('Location: resultats.php');
    exit;
}

if (jaHaVotat($email)) {
    afegeixMissatge('Ja has votat. Gràcies!');
    header('Location: resultats.php');
    exit;
}

// 2. Processament del POST
$errorVot = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opcioTriada = isset($_POST['opcio']) && is_string($_POST['opcio']) ? $_POST['opcio'] : '';

    if (!array_key_exists($opcioTriada, $opcions)) {
        $errorVot = 'Tria una de les opcions.';
    } else {
        guardaVot($email, $opcioTriada);
        afegeixMissatge('Vot registrat. Gràcies per participar!');
        header('Location: resultats.php');
        exit;
    }
}

require_once __DIR__ . '/inc/capcalera.php';
?>

<div class="card shadow-sm border-0 p-4">
    <h1 class="h4 fw-bold mb-3"><?= htmlspecialchars((string) $dades['consulta']['pregunta']) ?></h1>

    <?php if ($errorVot !== null): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($errorVot) ?></div>
    <?php endif; ?>

    <form method="POST" action="votacio.php">
        <div class="mb-4">
            <?php foreach ($opcions as $clau => $etiqueta): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="opcio" id="opcio_<?= htmlspecialchars((string) $clau) ?>" value="<?= htmlspecialchars((string) $clau) ?>">
                    <label class="form-check-label" for="opcio_<?= htmlspecialchars((string) $clau) ?>">
                        <?= htmlspecialchars((string) $etiqueta) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary fw-semibold">Confirmar el vot</button>
    </form>
</div>

<?php require_once __DIR__ . '/inc/peu.php'; ?>
