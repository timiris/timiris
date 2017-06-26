<?php
if(isset($_POST['idNature'])){
    require_once "../conn/connection.php";
    $res = $connection->query('SELECT * FROM ref_type_donnee WHERE fk_id_nature ='.(int)$_POST['idNature']);
    echo "<option value =''></option>";
    while($li = $res->fetch(PDO::FETCH_OBJ)){
        echo "<option value ='".$li->id."'>".strtoupper($li->libelle)."</option>";
    }
}
?>