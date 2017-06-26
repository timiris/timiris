<table class = "tableInfosClient">
    <tr>
        <th class = "tabs">Derniére Localisation</th>
        <td><?php echo $cellid ; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Derniére Recharge</th>
        <td><?php echo $montant_recharge; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier SMS Emis</th>
        <td><?php echo $dt_sms; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier SMS Reçu</th>
        <td><?php echo $dt_sms_recu; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier appel Emis</th>
        <td><?php echo $dt_appel_emis_total; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier appel Reçu</th>
        <td><?php echo $dt_appel_recu_total; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier transfert OUT</th>
        <td><?php echo $montant_transfere_out; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier transfert IN</th>
        <td><?php echo $montant_transfere_in; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Dernier service</th>
        <td><?php echo $service; ?></td>
    </tr>
    <tr>
        <th class = "tabs">Derniére Connexion (DATA)</th>
        <td><?php echo $dt_data; ?></td>
    </tr>
</table>