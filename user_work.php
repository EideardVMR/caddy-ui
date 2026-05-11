<div class="view">
    <div class="content small"">
<?php 


if(($_GET['sub'] ?? '') == 'toggle') {

    $user = UserDB::getUserByName($_GET['id']);

    $user->active = !$user->active;

    UserDB::saveUserList();
    
    header("Location: ?s=users");
    exit();
}



?>
    </div>
</div>  