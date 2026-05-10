<?php

class SysDB {

    public static $caddy_require_restart = false;

    static function save(){
        $data = [
            "caddy_require_restart" => self::$caddy_require_restart
        ];
        file_put_contents(FILE_SYS, json_encode($data));
    }

    static function read(){
        $data = file_get_contents(FILE_SYS);
        $data = json_decode($data);
        self::$caddy_require_restart = $data->caddy_require_restart ?? false;
    }

    static function getCaddyVersion() : string {
        $output = [];
        exec("caddy version", $output);
        return implode("\n", $output);
    }

    static function getNginxVersion() : string {
        $output = [];
        exec("nginx -v 2>&1", $output);
        return implode("\n", $output);
    }

    static function getCaddyConfig() : string {
        $output = [];
        exec("caddy validate --config " . CADDY_CFG . " 2>&1", $output);

        $tmp = '';
        foreach($output as $o) {
            $j = json_decode($o);
            if($j !== null && isset($j->level)) {
                $tmp .= date('Y/m/d H:i:s', $j->ts) . ' - ' . $j->level . ' - ' . $j->msg . "\n";
            }
        }

        return $tmp;
    }

    static function hasCaddyWriteAccess() : array{
        $tmp = [];
        $tmp[DIR_CADDY_HOSTS] = is_writable(DIR_CADDY_HOSTS);
        $files = scandir(DIR_CADDY_HOSTS);
        foreach($files as $file) {
            if($file == '.' || $file == '..') { continue; }
            $tmp[DIR_CADDY_HOSTS . $file] = is_writable(DIR_CADDY_HOSTS . $file);
        }
        return $tmp;
    }
    static function hasDBWriteAccess() : array{
        $tmp = [];
        $tmp[FILE_SYS] = is_writable(FILE_SYS);
        $tmp[FILE_USERS] = is_writable(FILE_USERS);
        $tmp[DIR_HOSTS] = is_writable(DIR_HOSTS);
        $files = scandir(DIR_HOSTS);
        foreach($files as $file) {
            if($file == '.' || $file == '..') { continue; }
            $tmp[DIR_HOSTS . $file] = is_writable(DIR_HOSTS . $file);
        }
        return $tmp;
    }

    static function getCaddyJournal() : string {
        $output = [];
        exec("journalctl -xeu caddy 2>&1", $output);
        return implode("\n", $output);
    }

    static function restartCaddy(): string|true {
    $output = [];
    $exitCode = 0;
    exec("sudo systemctl restart caddy 2>&1", $output, $exitCode);

    if($exitCode === 0) { return true; }

    return implode("\n", $output);
}

}

?>