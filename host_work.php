<div class="view">
    <div class="content small"">
<?php 


if(($_GET['sub'] ?? '') == 'delete') {
    $host = HostDB::getHostByName($_GET['id']);

    echo '<div class="message warning">Möchten Sie "' . $host->domain . '" wirklich löschen?</div>';
    echo '
        <br>
        <div style="display:flex; gap: 10px; justify-content: space-between;">
        <a class="btn btn-delete" href="?s=host_work&sub=delete&id=' . $host->domain . '&sure=1">Ja, löschen</a>
        <a class="btn btn-add" href="?s=hosts">Nein, zurück</a>
        </div>
    ';
}

if(($_GET['sub'] ?? '') == 'delete' && isset($_GET['sure'])) {
    $host = HostDB::getHostByName($_GET['id']);
    if($host !== false) {
        unlink(DIR_HOSTS . $host->domain . '.json');
        unlink(DIR_CADDY_HOSTS . $host->domain);
        SysDB::$caddy_require_restart = true;
        SysDB::save();
    }

    header("Location: ?s=hosts");
    exit();
}

if(($_GET['sub'] ?? '') == 'toggle') {
    $host = HostDB::getHostByName($_GET['id']);

    if(file_exists(DIR_CADDY_HOSTS. $host->domain)) {
        unlink(DIR_CADDY_HOSTS . $host->domain);
        SysDB::$caddy_require_restart = true;
        SysDB::save();
    } else {
        file_put_contents(DIR_CADDY_HOSTS . $host->domain, $host->generateCaddyfile());
        chmod(DIR_CADDY_HOSTS . $host->domain, 0660);
        SysDB::$caddy_require_restart = true;
        SysDB::save();
    }
    
    header("Location: ?s=hosts");
    exit();
}



?>
    </div>
</div>  