<?php

require_once 'connection.php';
require_once 'tbAllTables.php';

class AllTables {

    static function fn_insert($tn, $prfx, $allRq) {
        global $connection, $tbAllTables, $parc;
        $ln_pfx = strlen($prfx);
        foreach ($tn as $num) {
            foreach ($tbAllTables as $tb) {
                if (substr($tb, 0, $ln_pfx) == $prfx) {
                    if (isset($allRq[$num][$tb]))
                        $tReq = 'INSERT INTO ' . $tb . ' (numero, ' . implode(', ', array_keys($allRq[$num][$tb])) . ')
                            VALUES (\'' . $num . '\', ' . implode(', ', array_values($allRq[$num][$tb])) . ')';
                    else
                        $tReq = 'INSERT INTO ' . $tb . ' (numero) VALUES (\'' . $num . '\')';

//                    echo "\r\n <br>" . $tReq . ';';
                    $result = $connection->query($tReq);
                }
            }
        }
    }

    static function fn_update_coresp($dateChanged = array()) {
        global $connection, $tbAllTables;
        while (count($tbAllTables)) {
            foreach ($tbAllTables as $key => $tb) {
                if (substr($tb, 0, 13) != 'data_attribut') {
                    $req_lock = "select count(*) nbr from pg_class JOIN pg_locks on pg_class.oid = relation and relname = '" . $tb . "'";
                    $result = $connection->query($req_lock);
                    $lck = $result->fetch(PDO::FETCH_OBJ);
                    if (!$lck->nbr) {
//                        $tReq = 'UPDATE ' . $tb . ' SET ' . implode(', ', $dateChanged);
                        $tReq = 'UPDATE ' . $tb . ' SET ' . implode('=0, ', $dateChanged).'=0 WHERE ' . implode('+', $dateChanged).'<>0';
//                        $tReq = 'ALTER TABLE ' . $tb . ' DROP COLUMN ' . implode(', DROP COLUMN ', $dateChanged) . ',
//                            ADD COLUMN ' . implode(' bigint DEFAULT 0::bigint, ADD COLUMN ', $dateChanged) . ' bigint DEFAULT 0::bigint';
                        echo date('YmdHis')." ".$tReq;
                        $result = $connection->query($tReq);
                        echo " ".date('YmdHis')."\r\n";
//                      exit();
                        unset($tbAllTables[$key]);
                    }
                    else
                        echo $tb . " est verrouillée \r\n";
                }
                else
                    unset($tbAllTables[$key]);
            }
        }
    }

}

?>