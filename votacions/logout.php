<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/inc/funcions.php';
$temaActual = gestionaTema();

$nomUsuari = $_SESSION['usuari']['nom'] ?? 'usuari/ària';

// 1. Buidem l'array de sessió
$_SESSION = [];

// 2. Esborrem exclusivament la cookie de sessió
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Destruïm la sessió al servidor
session_destroy();
?>
<!DOCTYPE html>
<html lang="ca" data-bs-theme="<?= htmlspecialchars($temaActual) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessió tancada - Votacions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 <?= $temaActual === 'fosc' ? 'bg-dark text-light' : 'bg-light text-dark' ?>">

<main class="container flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm border-0 p-4 text-center" style="max-width: 450px;">
        <h1 class="h4 fw-bold mb-3">Fins la propera, <?= htmlspecialchars((string) $nomUsuari) ?>!</h1>
        <p class="text-muted mb-4">Has tancat la sessió correctament.</p>
        <a href="index.php" class="btn btn-primary">Tornar a entrar</a>
    </div>
</main>

<?php require_once __DIR__ . '/inc/peu.php'; ?>
