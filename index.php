<?php
ob_start();
require_once 'config.php';
require_once 'sys_db.php';
require_once 'users_db.php';
require_once 'hosts_db.php';


if (!UserDB::verifyAuth()) {
    include_once 'login.php';
    exit();
}

SysDB::read();

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Caddy-UI</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./style.css">
</head>

<body>

    <div class="view">
        <div class="content upper">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div><img src="./logo-dark.svg" alt="Logo" style="height: 50px; margin-right: 20px;"></div>

                <?php
                    if(SysDB::$caddy_require_restart) {
                        echo '
                            <div class="message warning">Caddy muss neu gestartet werden, damit die Änderungen wirksam werden. <a href="?s=sys&sub=restart_caddy">Jetzt neu starten</a></div>
                        ';
                    }
                ?>

                <div style="text-align: center">
                    <span style="font-size: 1.3em;">Hallo <?= UserDB::$current_user->name ?? 'Gast' ?></span><br>
                    <button class="btn btn-cancel" onclick="javascript: document.location='?s=logout'">Logout</button>    
                </div>
            </div>
        </div>
    </div>


    <div class="menu">
        <a href="?s=hosts" class="<?=(($_GET['s'] ?? '') === 'hosts' ? 'active' : '')?>">Hosts</a>
        <a href="?s=users" class="<?=(($_GET['s'] ?? '') === 'users' ? 'active' : '')?>">Users</a>
        <a href="?s=sys" class="<?=(($_GET['s'] ?? '') === 'sys' ? 'active' : '')?>">System</a>
    </div>

    <?php
        switch ($_GET['s'] ?? '') {
            case 'login':
                include_once 'login.php';
                break;
            case 'hosts':
                include_once 'hosts.php';
                break;
            case 'host_edit':
                include_once 'host_edit.php';
                break;
            case 'host_add':
                include_once 'host_add.php';
                break;
            case 'host_work':
                include_once 'host_work.php';
                break;
            case 'users':
                include_once 'users.php';
                break;
            case 'user_edit':
                include_once 'user_edit.php';
                break;
            case 'user_add':
                include_once 'user_add.php';
                break;
            case 'user_work':
                include_once 'user_work.php';
                break;
            case 'sys':
                include_once 'sys_detail.php';
                break;
            default:
                include_once 'hosts.php';
                break;
        }
    ?>
</body>

</html>


<?php
ob_end_flush();
?>