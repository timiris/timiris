<?php

require_once "../fn_security.php";
check_session();
?>
<table class = "tableInfosClient">
    <tr>
        <th class = "tabs">Date Activation ligne </th>
        <td><?php echo $dt_active; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Profil</th>
        <td><?php echo $profil; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Etat de la ligne</th>
        <td><?php echo $status; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Date fin activation</th>
        <td><?php echo $dt_active_stop; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Date fin suspension</th>
        <td><?php echo $dt_suspend_stop; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Date fin disable</th>
        <td><?php echo $dt_disable_stop; ?></td>
    </tr>
    <tr>
        <th class = "tabs">IMSI</th>
        <td><?php echo $imsi; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Langue de communication</th>
        <td><?php echo $langue; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Points de fidélité</th>
        <td><?php echo $points; ?></td>
    </tr>
</table>