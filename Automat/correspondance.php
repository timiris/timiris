<?php
$tb_crspd = $tb_init = array();
$req = 'SELECT * from historique_correspondance order by type, h_date';
try {
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)){
            $dt = str_replace('-', '', $ligne->h_date);
            $tb_crspd[$dt] = $ligne->champ;
            $tb_init[$dt] = array('msc'=> $ligne->msc, 'mgr'=> $ligne->mgr, 'rec'=> $ligne->rec, 'sms'=> $ligne->sms, 'data'=> $ligne->data, 'vou'=> $ligne->vou);
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}


function verif_init($tb_init, $cdrType, $dtFirst, $dtLast) {
    if($cdrType == 'mon' || $cdrType == 'clr')
        $cdrType = 'mgr';
    $ret = $dt = array();
    $dt[] = substr($dtLast, 0, 8);
    $dt[] = substr($dtLast, 4, 2);
    $dt[] = substr($dtLast, 0, 4);
    $dt[] = substr($dtFirst, 0, 8);
    $dt[] = substr($dtFirst, 4, 2);
    $dt[] = substr($dtFirst, 0, 4);
    foreach ($dt as $val){
        if (isset($tb_init[$val]) && !$tb_init[$val][$cdrType]){
            $ret[$val] = 1;
        }
        
    }
    return $ret;
}
?>