<div class="view">
    <div class="content small"">
        <?php
        $user = UserDB::getUserByName($_GET['id']);
        if (isset($_POST['save'])) {
            $user->name = $_POST['name'] ?? '';
            $user->username = $_POST['username'] ?? '';
            $user->active = (($_POST['active'] ?? '') == 'true');

            if (!empty($_POST['password']) || !empty($_POST['password_confirm'])) {
                if ($_POST['password'] != $_POST['password_confirm']) {
                    $user->errors[] = "Die Passwörter stimmen nicht überein";
                } else {
                    $user->setPassword($_POST['password']);
                }
            }

            if ($user->validate() === false) {
                foreach ($user->errors as $error) {
                    echo '<div class="message alert">' . $error . '</div>';
                }
            } else {
                if (UserDB::editUser($user)) {
                    echo '<div class="message success">Benutzer erfolgreich gespeichert</div>';
                    header("Location: ?s=users");
                } else {
                    foreach (UserDB::$errors as $error) {
                        echo '<div class="message alert">' . $error . '</div>';
                    }
                }
            }
        }

        if ($user === false) {
            echo '<div class="message alert">Datensatz nicht gefunden</div>';
        }
        ?>

        <form method="post" action="?s=user_edit&id=<?= $user->username ?>">

            <h3>Benutzer</h3>
            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="<?= $user ? $user->name : '' ?>" placeholder="z.B. John Doe">
            </div>

            <div class="field">
                <label for="username">Benutzername</label>
                <input id="username" name="username" type="text" value="<?= $user ? $user->username : '' ?>" placeholder="z.B. John.Doe" readonly>
            </div>

            <div class="field">
                <label for="active">Status</label>
                <select id="active" name="active">
                    <option value="true" <?= ($user && $user->active) ? 'selected' : '' ?>>Aktiviert</option>
                    <option value="false" <?= ($user && !$user->active) ? 'selected' : '' ?>>Deaktiviert</option>
                </select>
            </div>
            <h3>Benutzer</h3>
             <div class="field">
                <label for="password">Neues Passwort</label>
                <input id="password" name="password" type="password" value="" placeholder="Neues Passwort eingeben">
            </div>
             <div class="field">
                <label for="password_confirm">Passwort bestätigen</label>
                <input id="password_confirm" name="password_confirm" type="password" value="" placeholder="Passwort bestätigen">
            </div>

            

            <div style="display:flex; gap:10px; margin-top:1.5rem; justify-content: space-between;">
                <button class="btn btn-save" type="submit" name="save">
                    <i class="ti ti-device-floppy"></i>
                    Speichern
                </button>
                <button class="btn btn-cancel" type="button" onclick="javascript: document.location='?s=users';">
                    <i class="ti ti-x"></i>
                    Abbrechen
                </button>
            </div>
        </form>
    </div>
</div>