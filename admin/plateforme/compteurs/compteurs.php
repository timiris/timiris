<?php
$res = $connection->query('SELECT * FROM ref_compteurs WHERE fk_id_type in (' . $cnst.') ORDER BY libelle');
if ($res->rowCount()) {
    echo '<div style = "margin:auto; width : 98%; padding: 10px;">';
    echo "<table class ='dataTable display'>";
    echo "<thead><tr><th>Libelle</th><th style ='white-space: nowrap;text-align:right;text-align:center;'>Code OCS</th><th style ='white-space: nowrap;text-align:right;text-align:center;'>$titre</th><th style ='white-space: nowrap;text-align:right;text-align:center;'>Stats & Ciblage</th><th>Bonus</th></tr></thead><tbody>";
    $i = 0;
    while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
        if(!(int) $ligne->code_cmpt)
            continue;
        $cls = ($i%2)?'odd':'even';
        $i++;
        if($ligne->etat == 1){
            $etatStat = "Actif";
            $imgNameStat = 'on1';
            $titleStat = 'Désactiver';
        }else{
            $etatStat = "Incatif";
            $imgNameStat = 'off1';
            $titleStat = 'Activer';
        }
        if($ligne->bonus){
            $etatBns = "Actif";
            $imgNameBns = 'on1';
            $titleBns = 'Désactiver';
        }else{
            $etatBns = "Incatif";
            $imgNameBns = 'off1';
            $titleBns = 'Activer';
        }
        //$code=$cnst.'|'.$ligne->code_cmpt;
        $code=$ligne->code_cmpt;
        echo "<tr class=$cls><td><span class='spanEdit' name ='".$code."'>" . $ligne->libelle . "</span></td>
                <td>" . $ligne->code_cmpt . "</td>
                <td><span name='valeur_".$code."'>" . $ligne->valorisation . "</span> Ouguiya</td>
                <td style ='white-space: nowrap;text-align:right;text-align:center;'>
                    <img src = 'img/$imgNameStat.png' class = 'actionCmpt stat' name = '$code' title = '$titleStat'> 
                </td>
                <td style ='white-space: nowrap;text-align:right;text-align:center;'>
                    <img src = 'img/$imgNameBns.png' class = 'actionCmpt bns' name = '$code' title = '$titleBns'> 
                </td>
        </tr>";
    }
    echo "</tbody></table></div>";
}
?>
