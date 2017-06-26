<?php

if (isset($_POST['idTd'])) {
    require_once "../conn/connection.php";
    $default = array(60, 100, 1048576);
    $jsn = $connection->query('SELECT unite FROM ref_type_donnee WHERE id =' . (int) $_POST['idTd'])->fetch(PDO::FETCH_OBJ);
    $jsn = json_decode($jsn->unite, false);
    foreach ($jsn as $u => $l)
        if (in_array($u, $default))
            echo "<option value ='$u' selected>" . strtoupper($l) . "</option>";
        else
            echo "<option value ='$u'>" . strtoupper($l) . "</option>";
}
?>