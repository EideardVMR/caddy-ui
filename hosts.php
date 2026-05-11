<div class="view">
    <div class="content">
        <button class="btn btn-add" onclick="javascript: document.location='?s=host_add'">Host hinzufügen</button>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Ziel</th>
                    <th>SSL</th>
                    <th>Status</th>
                    <th style="max-width: 300px">Aktion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hosts = HostDB::getHostList();
                if($hosts === false) {
                    echo '<tr><td colspan="5">Keine Hosts gefunden</td></tr>';
                }
                foreach ($hosts as $host) {
                    echo '<tr>';
                    echo '
                        <td>
                            ' . $host->tableDomain() . '
                            <p class="subtext">' . $host->created_at . ' von ' . $host->created_by . '</p>
                        </td>
                    ';
                    echo '<td>' . $host->tableDestination() . '</td>';
                    echo '<td>' . $host->tableSSL() . '</td>';
                    echo '<td>' . ($host->getStatus() ? '<div class="banner on"><span class="dot on"></span>aktiviert</div>' : '<div class="banner off"><span class="dot off"></span>deaktiviert</div>') . '</td>';
                    echo '<td  style="max-width: 300px">
                                <a  class="btn_small btn-edit" href="?s=host_edit&id=' . $host->domain . '">Bearbeiten</a> 
                                <a class="btn_small btn-delete" href="?s=host_work&sub=delete&id=' . $host->domain . '">Löschen</a> 
                                <a class="btn_small btn-toggle" href="?s=host_work&sub=toggle&id=' . $host->domain . '">' . ($host->getStatus() ? 'Deaktivieren' : 'Aktivieren') . '</a>
                            </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <br>
        <button class="btn btn-add" onclick="javascript: document.location='?s=host_add'">Host hinzufügen</button>
    </div>
</div>