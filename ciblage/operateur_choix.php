<?php

require_once "../fn_security.php";
check_session();
if (isset($_POST["code"]) && !empty($_POST["code"])) {
    require_once "../conn/connection.php";
    $unite = $options = "";
    $rfAttr = explode(":", $_POST["code"]);
    $code = $rfAttr[0];
    $categorie = $rfAttr[1];
    $html = $rfAttr[2];
    $max_length = $rfAttr[3];
    $operateur = strtolower($_POST["operateur"]);
    $retour = array();
    // $divValRech = '<span>'.$ligne->libelle.' : </span>';
    $retour["sucess"] = 0;
    try {
        if (strtolower($html) == 'input') {
            $max = ($max_length != '') ? 'maxlength = "' . $max_length . '"' : '';
            $divValRech = '<input type = "text" id = "valeurCritere_' . $_POST["id"] . '" ' . $max . ' class = "critere" />';
        } elseif (strtolower($html) == 'select') {
            $req = "SELECT * FROM ref_liste_choix_attribut WHERE attribut = '$code'";
            $result = $connection->query($req);
            if ($operateur == 'in' || $operateur == 'not in')
                $divValRech = '<table style = "border : 1px solid black;blue; width:350px; height:30px;">
					<tr height = 5px><td>Choix possibles</td><td></td><td>Valeurs recherchées</td></tr>
                                            <tr><td rowspan = "2" width = "150px">
					<SELECT style ="width:150px;" id = "origine_' . $_POST["id"] . '">';
            else
                $divValRech = '<SELECT id = "valeurCritere_' . $_POST["id"] . '" class = "critere">';
            while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
                $divValRech .= "<option value = '" . $ligne->code . "'>" . ucfirst(strtolower($ligne->libelle)) . "</option>";
            }
            $divValRech .= "</SELECT>";
            if ($operateur == 'in' || $operateur == 'not in') {
                $divValRech .= "</td><td><img src = 'img/droite.png' name ='" . $_POST["id"] . "' class = 'choix_valeur'></td>";
                $divValRech .= '<td rowspan = "2">
				<SELECT style ="width:150px;" id = "valeurCritere_' . $_POST["id"] . '" class = "critere multiple">';
                $divValRech .= "</SELECT></td></tr>
				  <tr><td><img src = 'img/gauche.png' name ='" . $_POST["id"] . "' class = 'choix_valeur'></td></tr>
				</table>";
            }
        }
        $retour["divValRech"] = $divValRech;
        $retour["sucess"] = 1;
        echo json_encode($retour);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>