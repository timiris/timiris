<?php

require_once "../fn_security.php";
check_session();
?>
<table class = "tableInfosClient">
    <tr>
        <th class = "tabs">Nom</th>
        <td><?php echo $nom; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Numéro identification</th>
        <td><?php echo $nni; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Date de naissance </th>
        <td><?php echo $date_naissance; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Genre</th>
        <td><?php echo strtoupper($genre == 'F') ? 'Femme' : 'Homme'; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Balance</th>
        <td><?php echo $balance; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Date Activation ligne </th>
        <td><?php echo $dt_active; ?></td>
    </tr>
    <tr>
        <th class = "tabs">GFU</th>
        <td><?php echo $flotte; ?></td>
    </tr>
</table>