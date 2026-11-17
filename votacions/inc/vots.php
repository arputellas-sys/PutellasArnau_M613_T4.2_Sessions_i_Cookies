<?php
declare(strict_types=1);

const FITXER_VOTS = __DIR__ . '/../dades/vots.json';

/** Retorna tots els vots: ['email' => 'opcio', ...] */
function llegeixVots(): array {
    if (!file_exists(FITXER_VOTS)) {
        return [];
    }
    $contingut = file_get_contents(FITXER_VOTS);
    if ($contingut === false) {
        return [];
    }
    $vots = json_decode($contingut, true);
    return is_array($vots) ? $vots : [];
}

/** Guarda el vot d'un usuari */
function guardaVot(string $email, string $opcio): void {
    $vots = llegeixVots();
    $vots[$email] = $opcio;
    file_put_contents(FITXER_VOTS, json_encode($vots, JSON_PRETTY_PRINT), LOCK_EX);
}
