<?php

require_once 'config.php';
require_once 'sys_db.php';
require_once 'users_db.php';

$user = UserDB::getUserByName('admin');
$new_pw = strrand(15);

if($user === false) {
    echo "User 'admin' existiert nicht. User wird angelegt.\n";
    UserDB::addUser("admin", $new_pw);
    $user = UserDB::getUserByName('admin');
    $user->name = "Administrator";
    $user->active = true;
    UserDB::editUser($user);
} else {
    $user->active = true;
    $user->SetPassword($new_pw);
    UserDB::editUser($user);
}

echo "Das Passwort für den User 'admin' wurde zurückgesetzt. Das neue Passwort lautet: " . $new_pw . "\n";





?>