<?php
require_once 'connection.php';
$reqChargeAllTables = 'SELECT * FROM pg_catalog.pg_tables where tablename like \'data_%\' order by tablename';
try {
    $result = $connection->query($reqChargeAllTables);
    while ($table = $result->fetch(PDO::FETCH_OBJ)) {
        if ($table->tablename != 'data_attribut') {
            echo "\r\n Table : ".$table->tablename.' : '.date("YmdHis");
            $req_trunc = 'UPDATE ' . $table->tablename.' SET j1 = 0,  j2 = 0, j3 = 0, j4 = 0, j5 = 0, j6 = 0, j7 = 0, j8 = 0, j9 = 0, j10 = 0, j11 = 0, j12 = 0, j13 = 0, j14 = 0, j15 = 0, j16 = 0, j17 = 0, j18 = 0, j19 = 0, j20 = 0, j21 = 0, j22 = 0, j23 = 0, j24 = 0, j25 = 0, j26 = 0, j27 = 0, j28 = 0, j29 = 0, j30 = 0, j31 = 0, j32 = 0, m1 = 0, m2 = 0, m3 = 0, m4 = 0, m5 = 0, m6 = 0, m7 = 0, m8 = 0, m9 = 0, m10 = 0, m11 = 0, m12 = 0, m13 = 0, a1 = 0, a2 = 0, a3 = 0, a4 = 0, a5 = 0';
            $res_trunc = $connection->query($req_trunc);
             echo "\r\n Table : ".$table->tablename.' finished : '.date("YmdHis");
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>