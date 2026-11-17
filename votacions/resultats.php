<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/inc/protegeix.php';
require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

$usuari = $_SESSION['usuari'];
$email = (string) $usuari['email'];
$estaOberta = votacioOberta((string) $dades['consulta']['tancament']);

// 1. Comprovació de permisos per veure resultats
$potVeure = ($usuari['rol'] === 'admin') || !$estaOberta || jaHaVotat($email);

if (!$potVeure) {
    afegeixMissatge('Podràs veure els resultats quan hagis votat.');
    header('Location: votacio.php');
    exit;
}

// 2. Càlcul de resultats i participació
$vots = llegeixVots();
$opcions = $dades['consulta']['opcions'];
$recompteVots = recompte($vots, $opcions);
$totalVots = array_sum($recompteVots);

$votantsTotals = count(array_filter(
    $dades['usuaris'],
    fn(array $u): bool => ($u['rol'] ?? '') === 'votant'
));

require_once __DIR__ . '/inc/capcalera.php';
?>

<div class="card shadow-sm border-0 p-4">
    <h1 class="h4 fw-bold mb-2">Resultats de la votació</h1>
    <p class="text-muted mb-4"><?= htmlspecialchars((string) $dades['consulta']['pregunta']) ?></p>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Opció</th>
                    <th class="text-center" style="width: 120px;">Vots</th>
                    <th class="text-center" style="width: 120px;">Percentatge</th>
                    <th style="width: 35%;">Gràfic</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($opcions as $clau => $etiqueta): ?>
                    <?php
                    $numVots = $recompteVots[$clau] ?? 0;
                    $percentatge = $totalVots > 0 ? (int) round(($numVots / $totalVots) * 100) : 0;
                    ?>
                    <tr>
                        <td class="fw-medium"><?= htmlspecialchars((string) $etiqueta) ?></td>
                        <td class="text-center"><?= (int) $numVots ?></td>
                        <td class="text-center fw-bold"><?= (int) $percentatge ?>%</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= (int) $percentatge ?>%;" aria-valuenow="<?= (int) $percentatge ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="mt-3 mb-0 fw-semibold">
        Participació: <?= (int) $totalVots ?> de <?= (int) $votantsTotals ?> votants
    </p>
</div>

<?php require_once __DIR__ . '/inc/peu.php'; ?>
