<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Si no hi ha usuari a la sessió, redirigim amb error=acces
if (!isset($_SESSION['usuari']) || !is_array($_SESSION['usuari'])) {
    header('Location: index.php?error=acces');
    exit;
}

// 2. Comprovació de timeout per inactivitat (5 minuts = 300 segons)
// NOTA: Pots posar 30 temporalment per fer la Prova 14 i després tornar-ho a 300.
$timeout = 300;
$ultimaActivitat = $_SESSION['ultima_activitat'] ?? time();

if ((time() - $ultimaActivitat) > $timeout) {
    $_SESSION = [];
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
    session_destroy();
    header('Location: index.php?error=caducada');
    exit;
}

// 3. Actualitzem l'última activitat a la petició actual
$_SESSION['ultima_activitat'] = time();
