<?php
//récupération des champs des tables
require_once "../conn/connection.php";
require_once "../nameTables.php";
$tbRetour = $tabCorrespondance = $tbVal = array();
$allChTables = array();

try {
    $req = "SELECT * FROM historique_correspondance ORDER BY h_date ";
    $result = $connection->query($req);
    if ($result->rowCount()) {
        while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
            $tabCorrespondance[$ligne->champ] = $ligne->h_date;
        }
    }

    $tab_params = json_decode($_POST["parms"], true);
    $periodicite = $tab_params["periodicite"];
    $idBtn = $tab_params["id_button"];
    $unite = $tab_params["unite"];
    $unite_lib = $tab_params["unite_lib"];
    $entete = $tab_params["entete"];
    $cnd = '';
    if (isset($tab_params["numero"])) {
        $cnd = " WHERE numero = '222" . substr($tab_params["numero"], -8) . "'";
    }
    $sumColumns = array();
    switch ($periodicite) {
        case 'j':
            for ($i = 1; $i <= 32; $i++)
                $sumColumns[] = " SUM(j" . $i . ") as j" . $i;
            break;
        case 'm':
            for ($i = 1; $i < 14; $i++)
                $sumColumns[] = " SUM(m" . $i . ") as m" . $i;
            break;
        case 'a':
            for ($i = 1; $i < 6; $i++)
                $sumColumns[] = " SUM(a" . $i . ") as a" . $i;
            break;
    }
    $sumColumns = implode(', ', $sumColumns);
    // echo $sumColumns;
    if (count($tab_params["compteurs"]) == 1) {
        $graphMult = false;
        $compteur = $tab_params["compteurs"][0];
        if ($tab_params["nature"] == 13) {
            $tableName = $tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]];
            if (strtolower($compteur) != 'all')
                $cnd = " WHERE numero = '$compteur'";
        }elseif ($tab_params["nature"] == 14) {
            $tableName = $compteur;
            if (strtolower($tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]]) != 'all')
                $cnd = " WHERE numero = '" . $tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]] . "'";
//            $cnd = " WHERE numero = '".$tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]] ."'";
        }
        else
            $tableName = $tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]] . "_" . $compteur;
        $req = "SELECT $sumColumns from " . $tableName . $cnd;
//        echo $req;
//        exit();
        $result = $connection->query($req);
        if ($result->rowCount()) {
            $ligne = $result->fetch(PDO::FETCH_ASSOC);
            $nameClumns = array_keys($ligne);
            foreach ($nameClumns as $key => $nameClumn) {
                $tbVal[$tabCorrespondance[$nameClumn]] = ($ligne[$nameClumn] == null) ? 0 : $ligne[$nameClumn] / $unite;
            }
//		$tbRetour['val'] = $tbVal;
        }
    } else {
        $graphMult = true;
        foreach($tab_params["compteurs"] as $key => $v)
           $tab_params["compteurs"][$key] = strtolower($v); 
        $req_lib = "SELECT libelle, code_cmpt FROM ref_compteurs WHERE fk_id_type = " . $tab_params["type_donnee"] . " and lower(code_cmpt) in ('" . implode("','", $tab_params["compteurs"]) . "')";
        $result_lib = $connection->query($req_lib);
        $arr_lib_cmpt = array();
        while ($ligne = $result_lib->fetch(PDO::FETCH_OBJ)) {
            $arr_lib_cmpt[strtolower($ligne->code_cmpt)] = $ligne->libelle;
        }
//        print_r($tab_params["compteurs"]);
//        print_r($arr_lib_cmpt);
        //print $req_lib;
        foreach ($tab_params["compteurs"] as $compteur) {
            if ($tab_params["nature"] == 13) {
                $tableName = $tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]];
                if (strtolower($compteur) != 'all')
                    $cnd = " WHERE numero = '$compteur'";
                else
                    $cnd = '';
            }elseif ($tab_params["nature"] == 14) {
                $tableName = $compteur;
                if (strtolower($tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]]) != 'all')
                    $cnd = " WHERE numero = '" . $tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]] . "'";
                else
                    $cnd = '';
            }
            else
                $tableName = $tbNameTable[$tab_params["nature"] . "_" . $tab_params["type_donnee"]] . "_" . $compteur;
            $req = "SELECT $sumColumns from " . $tableName . $cnd;
//             echo $req;
//             exit();
            $result = $connection->query($req);
            if ($result->rowCount()) {
                $ligne = $result->fetch(PDO::FETCH_ASSOC);
                $nameClumns = array_keys($ligne);
                foreach ($nameClumns as $key => $nameClumn) {
                    $tbVal[$compteur][$tabCorrespondance[$nameClumn]] = ($ligne[$nameClumn] == null) ? 0 : $ligne[$nameClumn] / $unite;
                }
//		$tbRetour['val'] = $tbVal;
            }
        }
    }

    include 'diagramme_stats.php';
//	$tbRetour['exec'] = 1;
//	$tbRetour['req'] = $req;
//	echo json_encode($tbRetour);
} catch (PDOException $e) {
    echo $e->getMessage();
}


// require_once 'result_ciblage.php';
?>