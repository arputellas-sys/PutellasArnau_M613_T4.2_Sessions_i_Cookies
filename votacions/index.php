<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/inc/funcions.php';
$dades = require __DIR__ . '/inc/dades.php';

// 1. Si l'usuari ja té sessió iniciada, el portem directament a benvinguda.php
if (isset($_SESSION['usuari'])) {
    header('Location: benvinguda.php');
    exit;
}

$temaActual = gestionaTema();

// 2. Processar el formulari de Login quan s'envia per POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $contrasenya = isset($_POST['contrasenya']) && is_string($_POST['contrasenya']) ? $_POST['contrasenya'] : '';
    $recordar = isset($_POST['recordar']);

    $usuariTrobat = buscaUsuari($dades['usuaris'], $email);

    if ($usuariTrobat !== null && $usuariTrobat['contrasenya'] === $contrasenya) {
        // Credencials correctes: regenerem ID de sessió
        session_regenerate_id(true);

        // Guardem les dades de l'usuari sense la contrasenya
        $_SESSION['usuari'] = [
            'email' => $usuariTrobat['email'],
            'nom'   => $usuariTrobat['nom'],
            'rol'   => $usuariTrobat['rol'],
        ];
        $_SESSION['hora_login'] = time();
        $_SESSION['ultima_activitat'] = time();

        // Gestió de la cookie "Recorda el meu email"
        if ($recordar) {
            setcookie('email_recordat', $email, [
                'expires'  => time() + 30 * 24 * 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            setcookie('email_recordat', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        header('Location: benvinguda.php');
        exit;
    } else {
        header('Location: index.php?error=credencials');
        exit;
    }
}

// 3. Llegir i actualitzar la cookie de darrera visita (abans de qualsevol sortida HTML)
$ara = time();
$missatgeVisita = "Benvingut! És la teva primera visita.";

if (isset($_COOKIE['darrera_visita']) && is_string($_COOKIE['darrera_visita'])) {
    $tsVisita = filter_var($_COOKIE['darrera_visita'], FILTER_VALIDATE_INT);
    // Validem que sigui un enter vàlid, major que 0 i no una data futura
    if ($tsVisita !== false && $tsVisita > 0 && $tsVisita <= $ara) {
        $missatgeVisita = "La teva darrera visita va ser " . tempsDesDe($tsVisita) . ".";
    }
}

// Actualitzem la cookie darrera_visita amb l'hora actual (dura 30 dies)
setcookie('darrera_visita', (string) $ara, [
    'expires'  => $ara + 30 * 24 * 3600,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

// 4. Llegir i validar la cookie email_recordat
$emailRecordat = '';
$casellaMarcada = false;
if (isset($_COOKIE['email_recordat']) && is_string($_COOKIE['email_recordat'])) {
    $emailValidat = filter_var($_COOKIE['email_recordat'], FILTER_VALIDATE_EMAIL);
    if ($emailValidat !== false) {
        $emailRecordat = $emailValidat;
        $casellaMarcada = true;
    }
}

// 5. Missatges d'error per GET amb llista blanca
$missatgesError = [
    'credencials' => 'Credencials no vàlides.',
    'acces'       => "Primer has d'iniciar sessió.",
    'caducada'    => 'La sessió ha caducat per inactivitat.',
];
$codiError = isset($_GET['error']) && is_string($_GET['error']) ? $_GET['error'] : '';
$textError = $missatgesError[$codiError] ?? null;
?>
<!DOCTYPE html>
<html lang="ca" data-bs-theme="<?= htmlspecialchars($temaActual) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inici de sessió - Votacions 2n DAW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 <?= $temaActual === 'fosc' ? 'bg-dark text-light' : 'bg-light text-dark' ?>">

<main class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold text-center mb-2">🗳️ Votacions de l'Institut</h1>
                <p class="text-center text-muted small mb-4"><?= htmlspecialchars($missatgeVisita) ?></p>

                <?php if ($textError !== null): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($textError) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Correu electrònic</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($emailRecordat) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasenya" class="form-label fw-medium">Contrasenya</label>
                        <input type="password" class="form-control" id="contrasenya" name="contrasenya" required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="recordar" name="recordar" value="1" <?= $casellaMarcada ? 'checked' : '' ?>>
                        <label class="form-check-label" for="recordar">Recorda el meu email</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/inc/peu.php'; ?>