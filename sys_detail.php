<div class="view">
    <div class="content small"">

        <table>
            <tr>
                <td>Caddy Version:</td>
                <td><?= SysDB::getCaddyVersion() ?></td>
            </tr>     
            <tr>
                <td>Nginx Version:</td>
                <td><?= SysDB::getNginxVersion() ?></td>
            </tr>     
            <tr>
                <td>PHP Version:</td>
                <td><?= PHP_VERSION ?></td>
            </tr>
            <tr>
                <td>Schreibrechte Caddy-Files:</td>
                <td>
                    <?php
                        foreach(SysDB::hasCaddyWriteAccess() as $key => $val) {
                            if($val) {
                                echo '<p style="color:green">'.$key.'</p>';
                            } else {
                                echo '<p style="color:red">'.$key.'</p>';
                            }
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Schreibrechte db-verzeichnes:</td>
                <td>
                    <?php
                        foreach(SysDB::hasDBWriteAccess() as $key => $val) {
                            if($val) {
                                echo '<p style="color:green">'.$key.'</p>';
                            } else {
                                echo '<p style="color:red">'.$key.'</p>';
                            }
                        }
                    ?>
                </td>
            </tr>
        </table>
        <?php
            if($_GET['sub'] == 'restart_caddy') {
                $o = SysDb::restartCaddy();
                if($o === true) {
                    SysDB::$caddy_require_restart = false;
                    SysDB::save();
                    echo '<div class="message success">Caddy wurde neu gestartet</div>';   
                } else {
                    echo '<div class="message alert">Fehler beim Neustarten von Caddy. Es scheint ein Konfigurationsproblem zu geben.</div>';   
                    echo '<pre>'.$o.'</pre>';
                }
            }

            if($_GET['sub'] == 'validate_caddy_cfg') {
                echo '<pre>'.SysDB::getCaddyConfig().'</pre>';
            }

            if($_GET['sub'] == 'caddy_journal') {
                echo '<pre>'.SysDB::getCaddyJournal().'</pre>';
            }
            

        ?>

        <button class="btn btn-delete" onclick="javascript: document.location='?s=sys&sub=restart_caddy'">Caddy neu starten</button>
        <button class="btn btn-add" onclick="javascript: document.location='?s=sys&sub=validate_caddy_cfg'">Caddy Konfig prüfen</button>
        <button class="btn btn-save" onclick="javascript: document.location='?s=sys&sub=caddy_journal'">Caddy Journal ausgeben</button>


    </div>
</div>