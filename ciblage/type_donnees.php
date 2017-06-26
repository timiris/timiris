<?php

require_once "../fn_security.php";
check_session();
$rep = "../";
$idNature = 0;
if (isset($_POST["id_nature"]) && !empty($_POST["id_nature"])) {
    $idNature = $_POST["id_nature"];
}
require_once $rep . "conn/connection.php";
$req = "SELECT * FROM ref_type_donnee WHERE etat = 1 AND fk_id_nature = '" . $idNature . "' order by libelle";
// ORDER BY type ASC, poids DESC
try {
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            echo "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>