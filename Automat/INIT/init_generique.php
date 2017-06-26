<?php

try {
    require_once '../connection.php';
    $lastTables = $type . '.tbl';
    $logUser = '/tim_log/log_init/user_init_' . $type . '.log';
    $tbAllTables = $dateChanged = $h_date = array();
    $req = 'select champ, h_date from historique_correspondance where ' . $type . '=false';
    //echo date('YmdHis') . " " . $req. "\r\n";
    $result = $connection->query($req);
    if (!$result->rowCount())
        exit();
    while ($champ = $result->fetch(PDO::FETCH_OBJ)) {
        $dateChanged[] = $champ->champ;
        $h_date[] = $champ->h_date;
    }
    $req = "SELECT * FROM pg_catalog.pg_tables where tablename like '" . implode("%' or tablename like '", $arr_clause) . "%'";
    $result = $connection->query($req);
    //echo date('YmdHis') . " " . $req. "\r\n";

    while ($table = $result->fetch(PDO::FETCH_OBJ)) {
        $tbAllTables[] = $table->tablename;
    }
    $Cnt_table = count($tbAllTables);
    $fl_array = (is_file($lastTables)) ? file($lastTables) : array();
    if (!$fl_array)
        $fl_array = array();
    foreach ($fl_array as $key => $f)
        $fl_array[$key] = trim($f);
    $tbAllTables = array_diff($tbAllTables, $fl_array);
    $nbTable = 0;
    foreach ($tbAllTables as $key => $tb) {
        $req_lock = "select count(*) nbr from pg_class JOIN pg_locks on pg_class.oid = relation and relname = '" . $tb . "'";
        $result = $connection->query($req_lock);
        $lck = $result->fetch(PDO::FETCH_OBJ);
        if (!$lck->nbr) {
            $tReq = 'UPDATE ' . $tb . ' SET ' . implode('=0, ', $dateChanged) . '=0 WHERE ' . implode('+', $dateChanged) . '<>0';
            $result = $connection->query($tReq);
            //echo " " . date('YmdHis') . "\r\n";
            unset($tbAllTables[$key]);
            $nbTable++;
            $fl_array[] = $tb;
            $prc = count($fl_array);
//            echo date('YmdHis') . " ($prc/$Cnt_table) $tReq \r\n";
            $fo = fopen($logUser, 'a');
            if ($prc == 1) {
                $mes = "\r\n".date('YmdHis') . " Début Initialisation des dates : " . implode(", ", $h_date) . "\r\n";
                echo $mes;
                fputs($fo, $mes);
            }
            echo date('YmdHis') . " ($prc/$Cnt_table) $tb : OK\r\n";
            $ta = (100 * $prc / $Cnt_table);
            $ta = number_format($ta, 2, '.','');
            $mes = date('His') . " Taux d'initialisation est : $ta% \r\n";
            fputs($fo, $mes);

            file_put_contents($lastTables, implode("\n", $fl_array));
            fclose($fo);
        }
        else
            echo $tb . " est verrouillée \r\n";
        if ($nbTable > 10)
            break;
    }
    if (!count($tbAllTables)) {  // Mise a jours des champs de correspondance
        $req = "update historique_correspondance set $type=true where champ in ('" . implode("', '", $dateChanged) . "')";
        $mes = date('YmdHis') . " Fin Initialisation";
        echo $mes;
        $result = $connection->query($req);
        file_put_contents($lastTables, implode("\n", $tbAllTables));
        $fo = fopen($logUser, 'a');
        fputs($fo, $mes);
        fclose($fo);
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>