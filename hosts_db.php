<?php
class HostDB {

    private static $hosts = null;
    public static array $errors = [];
    static function getHostList() : array {

        if(self::$hosts !== null) { return self::$hosts; }

        $hostfiles = scandir(DIR_HOSTS);
        foreach( $hostfiles as $hostfile ) {
            if($hostfile == '.' || $hostfile == '..') { continue; }
            $host_raw = file_get_contents(DIR_HOSTS . $hostfile);
            $host_json = json_decode($host_raw);
            
            $host = new Host();
            $host->MapData($host_json);

            self::$hosts[$host->domain] = $host;
        }

        return self::$hosts;
    }

    static function getHostByName(string $domain) :false|Host {
        if(self::$hosts === null) { self::getHostList(); }
        if(!isset(self::$hosts[$domain])) { return false; }
        return self::$hosts[$domain];
    }

    static function addHost(Host $host) : bool|int {

        if(UserDB::$current_user === null) { return false; }

        self::$errors = [];

        $host->created_at = date("Y-m-d H:i:s");
        $host->created_by = UserDB::$current_user->username;
        $host->last_edit = date("Y-m-d H:i:s");
        $host_raw = json_encode($host);

        if(file_exists(DIR_HOSTS . $host->domain . '.json')) {
            return false;
        }

        if(!file_put_contents(DIR_HOSTS . $host->domain . '.json', $host_raw)) {
            self::$errors[] = "Fehler beim Speichern des Hosts.";
        } else {
            chmod(DIR_HOSTS . $host->domain . '.json', 0660);
        }

        if(count(self::$errors) == 0) {
            SysDB::$caddy_require_restart = true;
            SysDB::save();
        }

        return count(self::$errors) == 0;
    }
    
    static function editHost(Host $host) : bool|int {

        self::$errors = [];
        $file = file_get_contents(DIR_HOSTS . $host->domain . '.json');
        $host_json = json_decode($file);
        $host_old = new Host();
        $host_old->MapData($host_json);
        $host->versions[] = json_encode($host_old);
        $host->last_edit = date("Y-m-d H:i:s");
        $host_raw = json_encode($host);

        if(!file_put_contents(DIR_HOSTS . $host->domain . '.json', $host_raw)) {
            self::$errors[] = "Fehler beim Speichern des Hosts.";
        } else {
            chmod(DIR_HOSTS . $host->domain . '.json', 0660);
        }

        if($host-> getStatus() === true) {
            if(!file_put_contents(DIR_CADDY_HOSTS . $host->domain, $host->generateCaddyfile())) {
                self::$errors[] = "Fehler beim aktivieren des Hosts.";
            } else {
                chmod(DIR_CADDY_HOSTS . $host->domain, 0660);
            }
        }

        if(count(self::$errors) == 0) {
            SysDB::$caddy_require_restart = true;
            SysDB::save();
        }
        
        return count(self::$errors) == 0;

    }

}

class Host {

    const WAF_ON = 'On';
    const WAF_OFF = 'Off';
    const WAF_DetectionOnly = 'DetectionOnly';

    const SSL_OFF = 'Off';
    const SSL_LETSENCRYPT = 'LetsEncrypt';
    const SSL_CUSTOM = 'Custom';
    const SSL_CUSTOM_ACME = 'Custom_ACME';

    public string $domain = '';

    public bool $http_redirect = false;

    public string $waf_state = self::WAF_OFF;

    public string $destination_protocol = 'http';
    public string $destination_address = '';
    public int $destination_port = 80;

    public bool $destination_verify_tls = false;

    public bool $websocket_allow = true;

    public string $ssl_type = self::SSL_OFF;
    public string $ssl_email = '';
    public string $ssl_direcory_url = '';
    public string $ssl_key_id = '';
    public string $ssl_mac_key = '';

    public string $created_at = '';

    public string $created_by = '';

    public string $last_edit = '';

    public array $blocked_ips = [];

    public array $versions = [];

    public array $errors = [];
    function getStatus() : bool {
        return file_exists(DIR_CADDY_HOSTS . $this->domain);
    }

    function MapData(StdClass $data){
        $this->domain = $data->domain ?? '';
        $this->http_redirect = $data->http_redirect ?? true;
        $this->waf_state = $data->waf_state  ?? self::WAF_OFF;
        $this->destination_protocol = $data->destination_protocol ?? 'http';
        $this->destination_address = $data->destination_address ?? '';
        $this->destination_port = $data->destination_port ?? '';
        $this->websocket_allow = $data->websocket_allow ?? true;
        $this->ssl_type = $data->ssl_type ?? self::SSL_OFF;
        $this->ssl_email = $data->ssl_email ?? '';
        $this->ssl_direcory_url = $data->ssl_direcory_url ?? '';
        $this->ssl_key_id = $data->ssl_key_id ?? '';
        $this->ssl_mac_key = $data->ssl_mac_key ?? '';
        $this->created_at = $data->created_at ?? '';
        $this->created_by = $data->created_by ?? '';
        $this->last_edit = $data->last_edit ?? '';
        $this->versions = $data->versions ?? [];
    }

    function tableDomain() : string {
        $url = '';
        if($this->ssl_type != self::SSL_OFF) {
            $url .= 'https://' . $this->domain;
        } else {
            $url .= 'http://' . $this->domain;
        }
        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    }

    function validate() : bool {

        $this->errors = [];

        if(!self::validateDomain($this->domain)) {
            $this->errors[] = "Fehlerhafte Domain! Bitte kein Protokoll oder Pfade mit angeben.";
        }

        if(!filter_var($this->destination_address, FILTER_VALIDATE_IP) && !self::validateDomain($this->destination_address)) {
            $this->errors[] = "Ziel-Adresse: Bitte nur eine Domain oder IP angeben.";
        }

        if($this->destination_port < 0 || $this->destination_port > 65535) {
            $this->errors[] = "Ziel-Port: Bitte einen Port zwischen 0 und 65535 angeben.";
        }

        if(($this->destination_port == 80 && $this->destination_protocol == 'https') || ($this->destination_port == 443 && $this->destination_protocol == 'http'))
        {
            $this->errors[] = "Ziel-Port: HTTP als Protokoll und 443 als Port oder HTTPS als Protokoll und 80 als Port ist nicht sinnvoll.";
        }


        if(!filter_var($this->ssl_email, FILTER_VALIDATE_EMAIL) && ($this->ssl_type == self::SSL_LETSENCRYPT || $this->ssl_type == self::SSL_CUSTOM_ACME)) {
            $this->errors[] = "E-Mail Adresse: Wenn Sie ein Zertifikat über ACME (z.B. Let's Encrypt) beziehen möchten, muss eine gültige E-Mail Adresse angegeben werden.";
        }
        



        return count($this->errors) == 0;
    }
    
    static function validateDomain(string $input): bool {
        // Kein Protokoll, kein Slash, kein @
        if (preg_match('/[:\/@]/', $input)) {
            return false;
        }

        // Kein führender oder doppelter Punkt
        if (str_starts_with($input, '.') || str_contains($input, '..')) {
            return false;
        }

        if(!filter_var($input, FILTER_VALIDATE_DOMAIN)) {
            return false;
        }

        // Muss ein gültiger Hostname sein
        return count(explode('.', $input)) >= 2;
    }

    function tableDestination() : string{

        $tmp = '';
        if(($this->destination_port == 80 && $this->destination_protocol == 'https') || ($this->destination_port == 443 && $this->destination_protocol == 'http')) {
            $tmp .= '<span class="bad">'.$this->destination_protocol.'://'.$this->destination_address.':' . $this->destination_port . '</span>';
        } else {
            

            if($this->destination_protocol == 'http') {
                $tmp .= '<span class="ok">http</span>';
            }
            if($this->destination_protocol == 'https') {
                $tmp .= '<span class="good">https</span>';
            }

            $tmp .= '://'.$this->destination_address . ':' . $this->destination_port . '';
        }

        return $tmp;

    }

    function tableSSL() : string {
        $tmp = '';

        if($this->ssl_type == self::SSL_OFF) {
            $tmp = '<p class="bad">Off</p>';
        } else if($this->ssl_type == self::SSL_LETSENCRYPT) {
            $tmp = '<p class="good">Let\'s Encrypt</p>';
        } else if($this->ssl_type == self::SSL_CUSTOM) {
            $tmp = '<p class="ok">Custom</p>';
        } else if($this->ssl_type == self::SSL_CUSTOM_ACME) {
            $tmp = '<p class="ok">Custom_ACME</p>';
        } else {
            $tmp = '<p class="bad">Unknown</p>';
        }

        if($this->http_redirect) {
            $tmp .= '<p class="subtext good">Http Redirect aktiv</p>';
        } else {
            $tmp .= '<p class="subtext bad">Http Redirect inaktiv</p>';
        }

        return $tmp;
    }

    function getSubDomain() {
        $parts = explode('.', $this->domain);
        if(count($parts) > 2) {
            return implode('.', array_slice($parts, 0, -2));
        } else {
            return '';
        }
    }

    function getMainDomain() {
        $parts = explode('.', $this->domain);
        return implode('.', array_slice($parts, -2));
    }

    function generateCaddyfile() {
        $tmp = $this->domain . ' {';
        

        if(count($this->blocked_ips) > 0) {

            $tmp .= '
    # Geblockte IPs
    @blocked {
        remote_ip ' . implode(' ', $this->blocked_ips) . '
    }
    abort @blocked
            ';

        }


        // HTTP Redirect
        if($this->http_redirect === true && $this->ssl_type != self::SSL_OFF) {
            $tmp .= '
    # HTTP -> HTTPS Umleitung
	@http {
		protocol http
	}
	redir @http https://{host}{uri} permanent';
        }

        // SSL

        if($this->ssl_type == self::SSL_LETSENCRYPT) {
            $tmp .= '
    # TLS mit Let\'s Encrypt
	tls ' . $this->ssl_email . '
            ';
        } else if($this->ssl_type == self::SSL_CUSTOM_ACME) {
            $tmp .= '
    # TLS mit Custom ACME Directory
	tls ' . $this->ssl_email . ' {
        issuer acme {
            dir ' . $this->ssl_direcory_url . '
            email ' . $this->ssl_email . '
            eab {
                key_id  ' . $this->ssl_key_id . '
                mac_key ' . $this->ssl_mac_key . '
            }
        }
    }
            ';
        }
        
        

        $tmp .= '
    route {
        # WAF
        coraza_waf {
			load_owasp_crs
			directives `
                Include /etc/caddy/waf/crs-setup.conf
                Include /etc/caddy/waf/rules/*.conf
                SecRuleEngine ' . $this->waf_state . '
                SecRequestBodyAccess On
                SecResponseBodyAccess On
                SecAuditLog /var/log/caddy/waf-' . $this->domain . '.log
                SecAuditEngine RelevantOnly
            `
		}

        reverse_proxy ' . $this->destination_protocol . '://' . $this->destination_address . ':' . $this->destination_port . ' {

        ';
        
        if($this->destination_protocol == 'https' && $this->destination_verify_tls === false) {
            $tmp .= '
            # Ignoriere TLS Fehler beim Zielserver (z.B. selbstsigniertes Zertifikat)
            transport http {
                    tls_insecure_skip_verify
            }
            ';
        }

        $tmp .= '
			header_up X-Real-IP {remote_host}
			#header_up X-Forwarded-For {remote_host}
			#header_up X-Forwarded-Proto {scheme}
	    }
    }
        ';

        $tmp .= '

	# Zugriffs-Log
	log {
		output file /var/log/caddy/' . $this->domain . '-access.log
		format json
	}
}        
        ';

        return $tmp;
    }
}
?>