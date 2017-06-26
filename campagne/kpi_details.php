<?php

if (isset($_POST['idTd']) && isset($_POST['unite']) && isset($_POST['idCmp'])) {
    require_once "../conn/connection.php";
    $idCmp = (int) $_POST['idCmp'];
    $unite = (int) $_POST['unite'];
    $reqC = $connection->query('select nbr_cible, nbr_gc from app_campagne where id = ' . $idCmp);
    if ($reqC->rowCount()) {
        $liC = $reqC->fetch(PDO::FETCH_OBJ);
        $nbr_cible = $liC->nbr_cible;
        $nbr_gc = $liC->nbr_gc;
//        $nbr_gc = 1;
        if ($nbr_cible == 0)
            exit('<br><br><blockout class= "alert-box warning">nombre de la cible est éagl 0</blockout><br><br>');
        if ($nbr_gc == 0)
            exit('<br><br><blockout class= "alert-box warning">nombre du groupe de contrôle est éagl 0</blockout><br><br>');
    }
    $res = $connection->query("SELECT cmp.libelle, kpi.cible, kpi.gc FROM ref_type_donnee td
        JOIN ref_compteurs cmp on cmp.fk_id_type = td.id
        JOIN app_campagne_kpi kpi on kpi.fk_id_campagne = $idCmp and td.tablecode||'_'||lower(cmp.code_cmpt) = kpi.tbname
        WHERE fk_id_type =" . (int) $_POST['idTd'].' order by cmp.libelle');

    if ($res->rowCount()) {
        echo '<table align = "center" width=95% style="font-size: 12px;">';
        echo '<tr><th>Champ de calcul</th><th>Cible</th><th>Grp Contrôle</th><th>KPI</th></tr>';
        while ($li = $res->fetch(PDO::FETCH_OBJ)) {
            $cible = $li->cible / ($nbr_cible  * $unite);
            $gc = $li->gc / ($nbr_gc * $unite);
            if ($gc)
                $prct = ($cible - $gc) * 100 / $gc;
            else
                $prct = ($cible * 100);
            if ($prct > 0)
                $cls = ' green ';
            elseif ($prct < 0)
                $cls = ' red ';
            else
                $cls = ' black';
            echo "<tr><td>" . $li->libelle . "</td><td>" . number_format($cible, 3, '.', ' ') . "</td><td>" . number_format($gc, 3, '.', ' ') . "</td><td style='color:$cls'>" . number_format($prct, 3, '.', ' ') . " %</td></tr>";
        }
        echo '</table>';
    }
    else
        echo 'KPI non disponible';
}
?>