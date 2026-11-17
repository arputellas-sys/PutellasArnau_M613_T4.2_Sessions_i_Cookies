<?php
declare(strict_types=1);

require_once __DIR__ . '/vots.php';

/**
 * Cerca un usuari pel seu email dins l'array d'usuaris.
 */
function buscaUsuari(array $usuaris, string $email): ?array {
    foreach ($usuaris as $u) {
        if (isset($u['email']) && $u['email'] === $email) {
            return $u;
        }
    }
    return null;
}

/**
 * Retorna el temps transcorregut des d'un timestamp en format llegible (singular/plural).
 */
function tempsDesDe(int $moment): string {
    $segons = time() - $moment;
    if ($segons < 60) {
        return "fa menys d'un minut";
    }
    $minuts = intdiv($segons, 60);
    if ($minuts < 60) {
        return $minuts === 1 ? "fa 1 minut" : "fa $minuts minuts";
    }
    $hores = intdiv($minuts, 60);
    if ($hores < 24) {
        return $hores === 1 ? "fa 1 hora" : "fa $hores hores";
    }
    $dies = intdiv($hores, 24);
    return $dies === 1 ? "fa 1 dia" : "fa $dies dies";
}

/**
 * Comprova si encara no s'ha superat la data de tancament.
 */
function votacioOberta(string $tancament): bool {
    $timestampTancament = strtotime($tancament);
    return $timestampTancament !== false && time() < $timestampTancament;
}

/**
 * Indica si l'usuari amb aquest email ja té un vot registrat al fitxer JSON.
 */
function jaHaVotat(string $email): bool {
    $vots = llegeixVots();
    return isset($vots[$email]);
}

/**
 * Calcula el recompte de vots per a cada opció, inicialitzant totes les opcions a 0.
 */
function recompte(array $vots, array $opcions): array {
    $resultat = [];
    foreach ($opcions as $clau => $etiqueta) {
        $resultat[$clau] = 0;
    }
    foreach ($vots as $email => $opcioVotada) {
        if (array_key_exists($opcioVotada, $resultat)) {
            $resultat[$opcioVotada]++;
        }
    }
    return $resultat;
}

/**
 * Desa un missatge flash a la sessió.
 */
function afegeixMissatge(string $text): void {
    $_SESSION['flash'] = $text;
}

/**
 * Llegeix i elimina el missatge flash de la sessió perquè només es mostri un cop.
 */
function llegeixMissatge(): ?string {
    $missatge = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_string($missatge) ? $missatge : null;
}

/**
 * AMPLIACIÓ OPCIONAL: Gestiona el canvi i lectura de la cookie de tema (clar / fosc).
 */
function gestionaTema(): string {
    $temesPermesos = ['clar', 'fosc'];
    if (isset($_GET['tema']) && is_string($_GET['tema']) && in_array($_GET['tema'], $temesPermesos, true)) {
        $tema = $_GET['tema'];
        setcookie('tema', $tema, [
            'expires'  => time() + 30 * 24 * 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        return $tema;
    }
    $temaCookie = $_COOKIE['tema'] ?? 'clar';
    return (is_string($temaCookie) && in_array($temaCookie, $temesPermesos, true)) ? $temaCookie : 'clar';
}
