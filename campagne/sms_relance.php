<?php
try {
    $rep = '../';
    require_once $rep . "conn/connection.php";
    require_once $rep . "defs.php";
    $dt = date('YmdHi');
    $req = "SELECT id FROM app_campagne WHERE etat = " . CMP_ENCOURS . " AND dt_relance IS NOT NULL AND dt_relance <= '$dt'";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $idCmp = $ligne->id;
        $connection->beginTransaction();
        $connection->query("UPDATE app_campagne SET dt_relance = NULL, nbr_teasing = 0 WHERE id = $idCmp");
        $connection->query("UPDATE app_campagne_cible SET dt_last_teasing = dt_teasing, dt_teasing = NULL WHERE fk_id_campagne = $idCmp");
        $connection->commit();
    }
} catch (Exception $e) {
    $connection->rollBack();
    echo $e->getMessage() . "\n\r";
}
?>