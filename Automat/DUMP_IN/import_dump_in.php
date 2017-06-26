<?php

try {
//    require_once 'function_insert.php';
    require_once 'config.php';
    require_once 'fn.php';
    require_once '../connection.php';
    echo "\r\nDébut parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
    $tbFiles = scandir($rep_chargement);
    unset($tbFiles[0]);
    unset($tbFiles[1]);
    $tbConsid = array();
    $allRq = $client = $allRqAttr = $nbrFile = $af = array();
    foreach ($tbFiles as $key => $fileName) {
        if (strpos($fileName, $config['cdrName'], 0) === 0 && substr($fileName, -5) == '.list') {
            $tbConsid[] = $fileName;
        }
    }
    //****************************** DEB PARTIE TRAITEMENT *********************
    if (count($tbConsid)) {
        $strFileNames = "'" . implode("','", $tbConsid) . "'";
        $reqVerifFiles = 'SELECT fichier from app_dump_charge where fichier in (' . $strFileNames . ')';
        $resVerifFiles = $connection->query($reqVerifFiles);
        $tbTrt = $resVerifFiles->fetchAll(PDO::FETCH_COLUMN, 0);
        $tbConsid = array_diff($tbConsid, $tbTrt);

        foreach ($tbTrt as $fileName) {
            $fichier = $rep_chargement . $fileName;
            $nvEmpNom = $rep_doublons . $fileName;
            $fs = rename($fichier, $nvEmpNom);
        }
    }
    if (count($tbConsid)) {
        echo "\n\rChargement des fichiers , process : '" . getmypid() . "'";
        foreach ($tbConsid as $keyFile => $fileName) {
            $fichier = $rep_chargement . $fileName;
            $nbrLigne = 0;
//            echo "\n\rDébut : " . $fileName . " , process : '" . getmypid() . "' " . date('His');
            echo "\n\r" . $fileName . ", H:" . date('His');
            $origine_file = str_replace('din', '', $fileName);
            $expFO = explode('_', $origine_file);
            $origine_file = $expFO[0].'_ocs_subscriber_'.$expFO[1].'.list';
            $fp = fopen($fichier, 'r');
            $lg_max = $config['max'];
            while (!feof($fp)) {
                $li = fgets($fp, 4000);
                if (strlen($li > 100)) {
                    $nbrLigne++;
                    $ligne_explode = explode($config['separateur'], $li);
                    if (count($ligne_explode) >= $lg_max) {
//                        $lg_max = count($ligne_explode);
                        foreach ($config['pos'] as $key => $val) {
                            $client[$key] = $ligne_explode[$val];
                        }
                        if (isset($config['pfx'])) {
                            foreach ($config['pfx'] as $key => $val) {
                                $client[$key] = $val . $client[$key];
                            }
                        }
                        if (isset($config['inc'])) {
                            foreach ($config['inc'] as $key => $val) {
                                $client[$key] = $val + $client[$key];
                            }
                        }
                        $client['is_lang_ar'] = ($client['is_lang_ar'] == 2058)? 'true' : 'false';
                        $client['heure'] = substr($fileName, 3, 8) . '234500';
                        if(strlen($client['dt_active']) != 14)
                            $client['dt_active'] = '';
                        $client['dt_disable_stop'] = str_replace("\r", '', $client['dt_disable_stop']);
                        $client['dt_disable_stop'] = str_replace("\n", '', $client['dt_disable_stop']);
                        if (verifierNumeroNational($client['numero']))
                            $allRqAttr[$client['numero']] = $client;
                        else {
                            $fo = fopen($rep_file_rejected . $fileName, 'a');
                            fputs($fo, $li . "\r\n");
                            fclose($fo);
                        }
                    } else {//Ligne incomplete
                        fclose($fp);
                        echo "\r\n Le cdr numéro : $nbrLigne du fichier $fileName ne contient que " . count($ligne_explode) . ", alors que max est : $lg_max \r\n";
                        $nvEmpNom = $rep_file_rejected . $fileName;
                        $fs = rename($fichier, $nvEmpNom);
                        //$key = array_search($fileName, $array);
                        unset($tbConsid[$keyFile]);
                        echo "\r\nFichier cdr incomplet : $fileName";
                        $client = $allRqAttr = $nbrFile = $af = null;
                        $client = $allRqAttr = $nbrFile = $af = array();
                        break;
                    }
                }
            }
            fclose($fp);
            echo "\n\rDébut traitement, " . count($allRqAttr) . " nums, process : '" . getmypid() . "' H:" . date('His');
            $retExec = execute_attribut($allRqAttr, $connection);
            echo "\n\rFin traitement , process : '" . getmypid() . "' H:" . date('His');

            $reqFile = "INSERT INTO app_dump_charge (fichier, dt_chargement, nbr_ligne, source, nbr_upd, nbr_new, origine_file)
                       VALUES ('$fileName', '" . date('YmdHis') . "', $nbrLigne, '" . $config['source'] . "', " . $retExec['upd'] . ", " . $retExec['new'] . ", '$origine_file')";
            //echo "\n\r" . $reqFile;
            $connection->query($reqFile);
            $fichier = $rep_chargement . $fileName;
            $fs = unlink($fichier);
//            $nvEmpNom = $rep_sauv . $fileName;
//            $fs = rename($fichier, $nvEmpNom);
            echo "\n\rSuppression : " . $fileName . ", process : '" . getmypid() . "' " . date('His');
            $client = $allRqAttr = $nbrFile = $af = null;
            $client = $allRqAttr = $nbrFile = $af = array();
            $fileName = '';
            //****************************** FIN PARTIE TRAITEMENT *********************
            // *************************** PARTIE MAJ **********************************
        } // for consider
        // *************************** FIN PARTIE MAJ **********************************
    }
   echo "\r\nFin parsing, process : '" . getmypid() . "'  " . date("Ymd H:i:s");
} catch (PDOException $e) {
    $nvEmpNom = $rep_file_error . $fileName;
    $fs = rename($fichier, $nvEmpNom);
    echo "\r\n";
    echo($e->getMessage());
    echo "\r\n";
}
$connection = null;
?>