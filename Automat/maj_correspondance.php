<?php

require_once 'connection.php';
//require_once 'correspondance.php';
require_once 'function_insert.php';
try {
    date_default_timezone_set('Africa/Dakar');
    $dateJ = strtotime(date("Ymd"));                        //2016-11-30
    $dtJD = date("Y-m-d", strtotime("+1 days", $dateJ));    //2016-12-01
    $dateMP = date("m", strtotime("first day of next month", $dateJ));     //12
    $dateAP = date("Y", strtotime("+1 year", $dateJ));      //2017
    $reqMx = "SELECT  MAX( h_date) dmax, type FROM  historique_correspondance group by type";
    $result = $connection->query($reqMx);
    $arr_mx_dt = array();
    while ($ligne = $result->fetch(PDO::FETCH_OBJ))
        $arr_mx_dt[$ligne->type] = $ligne->dmax;

    if ($arr_mx_dt['j'] == $dtJD)
        exit();
    echo "Début Mise à jours de la correspndance : " . date('YmdHis') . "\r\n";
    $arr_dt = array('j' => array('nbr' => 32, 'vp' => date("Ymd", strtotime("+1 days", $dateJ)), 'inc' => 'days', 'frm' => 'Y-m-d'),
        'm' => array('nbr' => 13, 'vp' => date("Ym", strtotime("first day of next month", $dateJ)), 'inc' => 'month', 'frm' => 'Y-m'),
        'a' => array('nbr' => 5, 'vp' => date("Y", strtotime("+1 year", $dateJ)), 'inc' => 'year', 'frm' => 'Y'));
    $df_date = date_diff(date_create($dtJD), date_create($arr_mx_dt['j']));

    $arr_dt['j']['dif'] = ($df_date->days > 32 ) ? 32 : $df_date->days;                     //1
    $arr_dt['a']['dif'] = ($dateAP - $arr_mx_dt['a'] > 5 ) ? 5 : $dateAP - $arr_mx_dt['a']; //0

    $m_max = $dateMP - substr($arr_mx_dt['m'], -2) + ($dateAP - 1 - substr($arr_mx_dt['m'], 0, 4)) * 12;
    if ($m_max < 0)
        $m_max += 12;
    $arr_dt['m']['dif'] = ($m_max > 13 ) ? 13 : $m_max;
//    print_r($arr_dt);
    $dateChanged = $reqCorr = array();
    foreach ($arr_dt as $type => $arr_type) {
        if ($arr_type['dif']) {
            $req = "SELECT * FROM historique_correspondance where type = '$type' order by h_date LIMIT " . $arr_type['dif'];
            $result = $connection->query($req);
            $i = 1;
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//                $dateChanged[] = $ligne->champ . ' = 0';
                $dateChanged[] = $ligne->champ;
                $dt = substr($arr_mx_dt[$type] . '-01-01', 0, 10);
                $valprochaine = date($arr_type['frm'], strtotime("+$i " . $arr_type['inc'], strtotime($dt)));
                $reqCorr[] = "UPDATE  historique_correspondance SET sms = false, mgr = false, rec = false, 
                                data = false, vou = false, msc = false, h_date = '" . $valprochaine . "' 
                            WHERE champ = '" . $ligne->champ . "' ";
                $i++;
            }
        }
    }
//    print_r($dateChanged);
    // *************************************************************************

    if (count($dateChanged)) {
//        AllTables::fn_update_coresp($dateChanged);
        $connection->query("BEGIN");
        foreach ($reqCorr as $req) {
            $result = $connection->query($req);
        }
        require_once 'maj_attribut_profil_statut.php';
        $connection->query('COMMIT');
        echo "\r\n Fin Mise à jours de la correspndance : " . date('YmdHis') . "\r\n";
    }
} catch (PDOException $e) {
    $connection->query('ROLLBACK');
    echo $e->getMessage();
}
?>