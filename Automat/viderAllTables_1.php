<?php

$tbAllTables = array();
require_once 'connection.php';
$reqChargeAllTables = 'SELECT * FROM pg_catalog.pg_tables where tablename like \'data%\' and tablename not like \'data_attribut%\'';
try {
    $result = $connection->query($reqChargeAllTables);
    while ($table = $result->fetch(PDO::FETCH_OBJ)) {
        if ($table->tablename != 'data_attribut') {
            echo "\r\n <br>".$table->tablename.' en cours !!!'.date('Ymd H:i:s');
            $req_trunc = 'TRUNCATE TABLE ' . $table->tablename;
            $res_trunc = $connection->query($req_trunc);
            echo "\r\n <br>".$table->tablename.' finished'.date('Y-m-d H:i:s');

        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>