<div class="view">
    <div class="content">
        <button class="btn btn-add" onclick="javascript: document.location='?s=user_add'">User hinzufügen</button>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Benutzername</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th style="max-width: 300px">Aktion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = UserDB::getUserList();
                if($users === false) {
                    echo '<tr><td colspan="4">Keine Benutzer gefunden</td></tr>';
                }
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . $user->username . '</td>';
                    echo '<td>' . $user->name . '</td>';
                    echo '<td>' . ($user->active ? '<div class="banner on"><span class="dot on"></span>aktiviert</div>' : '<div class="banner off"><span class="dot off"></span>deaktiviert</div>') . '</td>';
                    echo '<td  style="max-width: 300px">
                                <a  class="btn_small btn-edit" href="?s=user_edit&id=' . $user->username . '">Bearbeiten</a>
                                <a class="btn_small btn-toggle" href="?s=user_work&sub=toggle&id=' . $user->username . '">' . ($user->active ? 'Deaktivieren' : 'Aktivieren') . '</a>
                            </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <br>
        <button class="btn btn-add" onclick="javascript: document.location='?s=user_add'">User hinzufügen</button>
    </div>
</div>