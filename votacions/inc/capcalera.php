<?php
declare(strict_types=1);
require_once __DIR__ . '/funcions.php';

$temaActual = gestionaTema();
$usuariActual = $_SESSION['usuari'] ?? null;
$missatgeFlash = llegeixMissatge();
?>
<!DOCTYPE html>
<html lang="ca" data-bs-theme="<?= htmlspecialchars($temaActual) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votacions 2n DAW - Institut Lacetània</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 <?= $temaActual === 'fosc' ? 'bg-dark text-light' : 'bg-light text-dark' ?>">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="benvinguda.php">🗳️ Votacions 2n DAW</a>
        <?php if ($usuariActual): ?>
            <div class="navbar-nav me-auto">
                <a class="nav-link text-white" href="benvinguda.php">Inici</a>
                <?php if (($usuariActual['rol'] ?? '') === 'votant'): ?>
                    <a class="nav-link text-white" href="votacio.php">Votar</a>
                <?php endif; ?>
                <a class="nav-link text-white" href="resultats.php">Resultats</a>
            </div>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small">👤 <?= htmlspecialchars((string) $usuariActual['nom']) ?> (<?= htmlspecialchars((string) $usuariActual['rol']) ?>)</span>
                <a href="?tema=<?= $temaActual === 'fosc' ? 'clar' : 'fosc' ?>" class="btn btn-sm btn-outline-light">
                    <?= $temaActual === 'fosc' ? '☀️ Clar' : '🌙 Fosc' ?>
                </a>
                <a href="logout.php" class="btn btn-sm btn-danger">Tancar sessió</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<main class="container flex-grow-1">
    <?php if ($missatgeFlash !== null): ?>
        <div class="alert alert-info shadow-sm">
            <?= htmlspecialchars($missatgeFlash) ?>
        </div>
    <?php endif; ?>
