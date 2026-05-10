<div class="view">
    <div class="content small"">
        <?php
        $host = HostDB::getHostByName($_GET['id']);
        if (isset($_POST['save'])) {
            $host->destination_protocol = $_POST['destination_protocol'];
            $host->destination_address = $_POST['destination_address'];
            $host->destination_port = $_POST['destination_port'];
            $host->destination_verify_tls = ($_POST['destination_verify_tls'] == 'true');
            $host->http_redirect = ($_POST['http_redirect'] == 'true');
            $host->waf_state = $_POST['waf_state'];
            $host->websocket_allow = ($_POST['websocket_allow'] == 'true');
            $host->ssl_type = $_POST['ssl_type'];
            $host->ssl_email = $_POST['ssl_email'];
            $host->ssl_key_id = $_POST['ssl_key_id'];
            $host->ssl_mac_key = $_POST['ssl_mac_key'];

            if ($host->validate() === false) {
                foreach ($host->errors as $error) {
                    echo '<div class="message alert">' . $error . '</div>';
                }
            } else {
                if (HostDB::editHost($host)) {
                    echo '<div class="message success">Host erfolgreich gespeichert</div>';
                    header("Location: ?s=hosts");
                } else {
                    foreach (HostDB::$errors as $error) {
                        echo '<div class="message alert">' . $error . '</div>';
                    }
                }
            }
        }

        if ($host === false) {
            echo '<div class="message alert">Datensatz nicht gefunden</div>';
        }
        ?>

        <form method="post" action="?s=host_edit&id=<?= $host->domain ?>">

        <h3>Domain</h3>
        <?php if (DOMAINS === false): ?>
            <div class="field">
                <label for="fulldomain">Domain</label>
                <input id="fulldomain" name="fulldomain" type="text" value="<?= $host ? $host->domain : '' ?>" placeholder="z.B. test.example.de" readonly>
            </div>
        <?php else: ?>
            <div style="display:flex; justify-content: space-between; gap: 10px">
                <div class="field" style="width: 50%">
                    <label for="subdomain">Sub-Domain</label>
                    <input id="subdomain" name="subdomain" type="text" value="<?= $host ? $host->getSubDomain() : '' ?>" placeholder="z.B. test" readonly>
                </div>
                <div class="field" style="width: 50%">
                    <label for="domain">Domain</label>
                    <select id="domain" name="domain" readonly>
                        <option value="none">Domain auswählen</option>
                        <?php foreach (DOMAINS as $domain): ?>
                            <option value="<?= $domain ?>" <?= (($host && $domain == $host->getMainDomain()) ? 'selected' : '') ?>><?= $domain ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <h3>Ziel</h3>
        <div style="display:flex; justify-content: space-between; gap: 10px">
            <div class="field" style="width: 200px;">
                <label for="destination_protocol">Protokoll</label>
                <select id="destination_protocol" name="destination_protocol">
                    <option value="http" <?= ($host && $host->destination_protocol == 'http') ? 'selected' : '' ?>>HTTP</option>
                    <option value="https" <?= ($host && $host->destination_protocol == 'https') ? 'selected' : '' ?>>HTTPS</option>
                </select>
            </div>
            <div class="field" style="width: 100%;">
                <label for="destination_address">Adresse</label>
                <input id="destination_address" name="destination_address" type="text" value="<?= $host ? $host->destination_address : '' ?>" placeholder="z.B. 192.168.0.100">
            </div>
            <div class="field" style="width: 200px;">
                <label for="destination_port">Port</label>
                <input id="destination_port" name="destination_port" type="text" value="<?= $host ? $host->destination_port : '' ?>" placeholder="z.B. 8080">
            </div>
        </div>

        <div id="dest-extra-fields" style="display: <?= (($host && $host->destination_protocol == 'https') ? 'block' : 'none') ?>">
            <label for="destination_verify_tls">Validiere Zielzertifikat</label>
            <select id="destination_verify_tls" name="destination_verify_tls">
                <option value="true" <?= ($host && $host->destination_verify_tls) ? 'selected' : '' ?>>Aktiv (Zertifikat wird validiert)</option>
                <option value="false" <?= ($host && !$host->destination_verify_tls) ? 'selected' : '' ?>>Inaktiv (Zertifikat wird nicht validiert)</option>
            </select>
        </div>

        <h3>WAF</h3>
        <label for="waf_state">Web Application Firewall</label>
        <select id="waf_state" name="waf_state">
            <option value="<?= Host::WAF_ON ?>" <?= ($host && $host->waf_state == Host::WAF_ON) ? 'selected' : '' ?>>Aktiv</option>
            <option value="<?= Host::WAF_OFF ?>" <?= ($host && $host->waf_state == Host::WAF_OFF) ? 'selected' : '' ?>>Inaktiv</option>
            <option value="<?= Host::WAF_DetectionOnly ?>" <?= ($host && $host->waf_state == Host::WAF_DetectionOnly) ? 'selected' : '' ?>>Nur Erkennung</option>
        </select>

        <h3>Websocket</h3>
        <label for="websocket_allow">Websocketunterstützung</label>
        <select id="websocket_allow" name="websocket_allow">
            <option value="true" <?= ($host && $host->websocket_allow) ? 'selected' : '' ?>>Aktiv</option>
            <option value="false" <?= ($host && !$host->websocket_allow) ? 'selected' : '' ?>>Inaktiv</option>
        </select>

        <h3>SSL</h3>
        <label for="ssl_type">SSL Zertifikat</label>
        <select id="ssl_type" name="ssl_type">
            <option value="<?= Host::SSL_OFF ?>" <?= ($host && $host->ssl_type == Host::SSL_OFF) ? 'selected' : '' ?>>Kein Zertifikat</option>
            <option value="<?= Host::SSL_LETSENCRYPT ?>" <?= ($host && $host->ssl_type == Host::SSL_LETSENCRYPT) ? 'selected' : '' ?>>Let's Encrypt</option>
            <option value="<?= Host::SSL_CUSTOM ?>" <?= ($host && $host->ssl_type == Host::SSL_CUSTOM) ? 'selected' : '' ?>>Custom (aktuell nicht unterstützt)</option>
            <option value="<?= Host::SSL_CUSTOM_ACME ?>" <?= ($host && $host->ssl_type == Host::SSL_CUSTOM_ACME) ? 'selected' : '' ?>>Custom ACME</option>
        </select>

        <div id="ssl-extra-fields-1" style="display: <?= (($host && $host->ssl_type !== Host::SSL_OFF) ? 'block' : 'none') ?>">
            <label for="http_redirect">HTTP redirect to HTTPS</label>
            <select id="http_redirect" name="http_redirect">
            <option value="true" <?= ($host && $host->http_redirect) ? 'selected' : '' ?>>Aktiv</option>
            <option value="false" <?= ($host && !$host->http_redirect) ? 'selected' : '' ?>>Inaktiv</option>
            </select>
            <div class="field">
                <label for="ssl_email">E-Mail</label>
                <input id="ssl_email" name="ssl_email" type="text" value="<?= $host ? $host->ssl_email : '' ?>" placeholder="z.B. max.mustermann@example.de">
            </div>
        </div>

        <div id="ssl-extra-fields-2" style="display: <?= (($host && $host->ssl_type === Host::SSL_CUSTOM_ACME) ? 'block' : 'none') ?>">
            <div class="field">
                <label for="ssl_direcory_url">Directory URL</label>
                <input id="ssl_direcory_url" name="ssl_direcory_url" type="text" value="<?= $host ? $host->ssl_direcory_url : '' ?>" placeholder="z.B. https://acme-v02.api.letsencrypt.org/directory">
            </div>

            <div class="field">
                <label for="ssl_key_id">Key ID</label>
                <input id="ssl_key_id" name="ssl_key_id" type="text" value="<?= $host ? $host->ssl_key_id : '' ?>" placeholder="z.B. aBcDeFgH1234567890">
            </div>

            <div class="field">
                <label for="ssl_mac_key">MAC Key</label>
                <input id="ssl_mac_key" name="ssl_mac_key" type="text" value="<?= $host ? $host->ssl_mac_key : '' ?>" placeholder="z.B. aB3dEfGhIjKlMnOpQrStUvWxYz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcd">
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:1.5rem; justify-content: space-between;">
            <button class="btn btn-save" type="submit" name="save">
                <i class="ti ti-device-floppy"></i>
                Speichern
            </button>
            <button class="btn btn-cancel" type="button" onclick="javascript: document.location='?s=hosts';">
                <i class="ti ti-x"></i>
                Abbrechen
            </button>
        </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('ssl_type').addEventListener('change', function() {
        const extra1 = document.getElementById('ssl-extra-fields-1');
        const extra2 = document.getElementById('ssl-extra-fields-2');

        if (this.value == '<?= Host::SSL_CUSTOM_ACME ?>') {
            extra2.style.display = 'block';
        } else {
            extra2.style.display = 'none';
        }

        if (this.value == '<?= Host::SSL_LETSENCRYPT ?>' || this.value == '<?= Host::SSL_CUSTOM_ACME ?>') {
            extra1.style.display = 'block';
        } else {
            extra1.style.display = 'none';
        }
    });

    document.getElementById('destination_protocol').addEventListener('change', function() {
        const extra = document.getElementById('dest-extra-fields');

        if (this.value == 'https') {
            extra.style.display = 'block';
        } else {
            extra.style.display = 'none';
            document.getElementById('destination_verify_tls').value = 'false';
        }
    });
</script>