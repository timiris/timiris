<?php

require_once 'connection.php';
require_once 'correspondance.php';
$dj = date('Ymd');
$dm = date('Ym');
$da = date('Y');
$dt = getDate();
$jm = $dt['mday'];
$ja = $dt['yday'];
if (!isset($tb_crspd[$dj]))
    exit('Table correspondance n\'est pas mise a jour !!');
echo "\r\nDébut Mise à jours des profils et statuts : " . date('YmdHis') . "\r\n";
$req_seg = "SELECT profil, status, count(*) nbr FROM data_attribut  GROUP BY profil, status";
$result = $connection->query($req_seg);
$ret = $ligne = $result->fetchAll(PDO::FETCH_OBJ);
$arr_status = array(0 => 'idle', 1 => 'active', 2 => 'suspend', 3 => 'disable', 4 => 'pool', 7 => 'pool');
$arr_all = array();

foreach ($ret as $li) {
    if (isset($arr_status[$li->status])) {
        $MyM = ($tb_crspd[$dm] == 0) ? $li->nbr : ($tb_crspd[$dm] * ($jm - 1) + $li->nbr) / $jm;
        $MyA = ($tb_crspd[$da] == 0) ? $li->nbr : ($tb_crspd[$da] * ($ja - 1) + $li->nbr) / $ja;

        $req_up = 'update  data_attribut_profil_' . $arr_status[$li->status] . ' set 
            ' . $tb_crspd[$dj] . ' =  ' . $li->nbr . ',
            ' . $tb_crspd[$dm] . ' =  ' . $MyM . ',
            ' . $tb_crspd[$da] . ' =  ' . $MyA . "
        where numero = '" . $li->profil . "'";
        $res_up = $connection->prepare($req_up);
        $res_up->execute();
        if (!$res_up->rowCount()) {
            $req_up = "insert into data_attribut_profil_" . $arr_status[$li->status] . " (numero, " . $tb_crspd[$dj] . ", " . $tb_crspd[$dm] . ", " . $tb_crspd[$da] . ") VALUES
                ('" . $li->profil . "', " . $li->nbr . ", " . $li->nbr . ", " . $li->nbr . ")";
            $res_up = $connection->query($req_up);
        }
        // echo $req_up."<br>";
        $arr_all[$li->profil] = isset($arr_all[$li->profil]) ? $arr_all[$li->profil] + $li->nbr : $li->nbr;
    }
}

foreach ($arr_all as $key => $val) {
    $MyM = ($tb_crspd[$dm] == 0) ? $val : ($tb_crspd[$dm] * ($jm - 1) + $val) / $jm;
    $MyA = ($tb_crspd[$da] == 0) ? $val : ($tb_crspd[$da] * ($ja - 1) + $val) / $ja;
    $req_up = 'update  data_attribut_profil_all set 
        ' . $tb_crspd[$dj] . ' =  ' . $val . ', 
        ' . $tb_crspd[$dm] . ' =  ' . $MyM . ',
        ' . $tb_crspd[$da] . ' =  ' . $MyA . " 
        where numero = '" . $key . "'";
    $res_up = $connection->prepare($req_up);
    $res_up->execute();
    if (!$res_up->rowCount()) {
        $req_up = "insert into data_attribut_profil_all (numero, " . $tb_crspd[$dj] . ", " . $tb_crspd[$dm] . ", " . $tb_crspd[$da] . ") VALUES
                ('" . $key . "', " . $val . ", " . $val . ", " . $val . ")";
        $res_up = $connection->query($req_up);
    }
}
echo "Fin Mise à jours des profils et statuts : " . date('YmdHis') . "\r\n";
?>