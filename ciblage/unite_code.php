<?php

require_once "../fn_security.php";
check_session();
if (isset($_POST["code"]) && !empty($_POST["code"])) {
    require_once "../conn/connection.php";
    $options = "";
    $code = strtolower($_POST["code"]);
    $tp_dn = explode("_", $_POST["tp_dn"])[1];
    $req = "SELECT * FROM ref_compteurs WHERE lower(code_cmpt)='$code' AND fk_id_type = '$tp_dn'";
//	echo $req;
    try {
        $result = $connection->query($req);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $tabUnite = json_decode($ligne->unite, true);
            foreach ($tabUnite as $u => $l) {
                $options .= '<option value =' . $u . '>' . $l . '</option>';
            }
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo $options;
}
?>