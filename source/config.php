<?php
define("FILE_USERS", '/var/www/caddy-ui/db/users.json');
define("FILE_SYS", '/var/www/caddy-ui/db/sys.json');
define("DIR_HOSTS", '/var/www/caddy-ui/db/hosts/');
define("DIR_CADDY_HOSTS", '/etc/caddy/sites/');
define("CADDY_CFG", '/etc/caddy/Caddyfile');
define("DOMAINS", ['michelhp.de']);
//define("LOG_FILE", '/var/www/caddy-ui/std.log');
//define("LOG_LEVEL", 3); // 1 = nur Fehler, 2 = auch Warnungen,  3 = auch Informationen


/**
 * Erzeugt eine lesbare ausgabe eines Objektes
 * @param mixed $debug Objekt welches visualisiert werden soll.
 */
function print_debug($debug){
    echo str_replace(array('&lt;?php&nbsp;','?&gt;'), '', highlight_string( '<?php '. var_export($debug, true) .' ?>', true ) ).'<br>';
}

/**
 * strrand()
 * Generiert einen zufälligen String mit einer gewissen länge
 * 
 * @param mixed $laenge Länge der zu erstellenden Zeichenfolge
 * @param bool $signs Zeichen erlaubt
 * @param bool $upper Großbuchstaben erlaubt
 * @param bool $lower Kleinbuchstaben erlaubt
 * @param bool $number Zahlen erlaubt
 * @return string
 */
function strrand(int $laenge, bool $signs = false, bool $upper = true, bool $lower = true, bool $number = true) {
	$zeichen = "";
    if($number)
	   $zeichen = '0123456789';
    if($lower)
	   $zeichen .= 'abcdefghijklmnopqrstuvwxyz';
    if($upper)
	   $zeichen .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if($signs)
        $zeichen .= '()!.#:=-%';

    if(empty($zeichen))
        trigger_error("No charset selected", E_USER_ERROR);
    if($laenge<1)
        trigger_error("Lenght of $laenge not allowed", E_USER_ERROR);

	$str = '';
	$anz = strlen($zeichen);
	for ($i=0; $i<$laenge; $i++) {
	  $str .= $zeichen[mt_rand(0,$anz-1)];
	}
	return $str;
}
?>