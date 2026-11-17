# TASCA-04: Votacions de l'institut (M0613 - 2n DAW)

## Preguntes de reflexió

1. Per què guardem qui ha votat al servidor (al fitxer de vots) i no en una cookie `ha_votat`? Què podria fer un alumne si ho guardéssim en una cookie?**
Perquè les cookies s'emmagatzemen al navegador del client i l'usuari les pot modificar o esborrar fàcilment des de les eines de desenvolupador (F12) o obrint una finestra privada. Si es guardés en una cookie, qualsevol alumne podria esborrar-la després de votar i emetre tants vots com volgués.

2. Què passa si un usuari canvia el valor de la cookie `darrera_visita` a les eines del navegador? Com ho heu controlat?**
Si l'usuari hi escriu text, un número negatiu, un 0 o una data futura, la validació del servidor ho detecta i ho descarta, mostrant el missatge de primera visita sense donar cap Warning. S'ha controlat utilitzant `filter_var` amb `FILTER_VALIDATE_INT` i comprovant que el valor sigui major que 0 i menor o igual a `time()`.

3. Quina diferència hi ha entre el timeout de 5 minuts de la sessió i els 30 dies de la cookie `darrera_visita`? On es guarda cada dada?**
El timeout de 5 minuts controla la inactivitat de la sessió d'autenticació per seguretat i les seves dades (`$_SESSION`) es guarden al servidor. En canvi, la cookie `darrera_visita` es guarda al navegador de l'usuari durant 30 dies per recordar una dada no sensible entre diferents visites encara que es tanqui el navegador.

4. A `index.php`, per què no podem mostrar l'hora que acabem de guardar a `darrera_visita` llegint `$_COOKIE['darrera_visita']` a la mateixa petició?**
Perquè `setcookie()` només envia una capçalera HTTP (`Set-Cookie`) al navegador demanant-li que guardi la dada per a futures peticions. L'array superglobal `$_COOKIE` només conté les cookies que el navegador ja ha enviat en la petició actual, de manera que el nou valor no hi apareixerà fins a la càrrega següent.
