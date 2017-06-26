<?php

if (!isset($rep))
    $rep = "../../";
require_once $rep . "fn_security.php";
check_session();
$retour = array();
$retour['exec'] = 0;
$retour['cmp'] = '';
$retour['unite'] = '';
if (isset($_POST["natBonus"]) and !empty($_POST["natBonus"])) {
    $idType = $_POST["natBonus"];
    require_once $rep . 'conn/connection.php';
    $req = 'select code_cmpt as id, libelle from ref_compteurs where fk_id_type = ' . (int) $idType . ' and bonus = true order by libelle';
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            if ((int) $ligne->id)
                $retour['cmp'] .= "<option value = " . $ligne->id . ">" . ucfirst(strtolower($ligne->libelle)) . "</option>";
        }
    }
    if ($_POST["typeBonus"] == 1) {
        $req = 'select unite from ref_nature_bonus where id = ' . (int) $idType;
        $result = $connection->query($req);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_OBJ);
            $uniteTab = json_decode($ligne->unite, true);
            if (count($uniteTab))
                foreach ($uniteTab as $u => $l) {
                    $sel = ($u == 60 || $u == 100 || $u == 1048576) ? ' selected ' : '';
                    $retour['unite'] .= '<option value =' . $u . ' ' . $sel . '>' . $l . '</option>';
                }
        }
    } else {
        require_once 'config.php';
        $natBonus = $_POST["natBonus"];
        $idNature = (int) $_POST["idNatureTrafic"];
        if (substr($_POST['typeDec'], 0, 6) == 'cumule')
            $arrConf = $conf_bonus_pp_cumule;
        else
            $arrConf = $conf_bonus_pp;
            
        if ($idNature)
            foreach ($arrConf[$idNature][$natBonus] as $k => $v)
                $retour['unite'] .= '<option value =' . $k . ' >' . $v . '</option>';
    }
    $retour['exec'] = 1;
    echo json_encode($retour);
}
?>