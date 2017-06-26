<?php

try {
    $ftp_server = '10.86.3.67';
    $ftp_user_name = 'ocscdr';
    $ftp_user_pass = 'bill';
    $lastFiles1 = '/tim_DATA/cdrs/chargement/in/lastFiles1.unl';
    $lastFiles2 = '/tim_DATA/cdrs/chargement/in/lastFiles2.unl';
    $rep_depo = '/tim_DATA/cdrs/chargement/in/';
    $rep_sauvegarde = '/tim_arch/sauvegarde/in/';
    $maps = array('rec' => 'rec', 'dat' => 'data', 'vou' => 'vou', 'mgr' => 'mgr', 'sms' => 'sms');
    echo "\r\nDébut ftp " . date('Y-m-d H:i:s');

    $conn_id = ftp_connect($ftp_server);
// Identification avec un nom d'utilisateur et un mot de passe
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// Véfication de la connexion
    if ((!$conn_id) || (!$login_result)) {
        die("Echec de la connexion FTP !");
    }


    function getFiles($fl_array, $lastFiles, $cbp) {
        global $rep_depo, $conn_id, $maps;
        $dtj = date('Ymd');
        $dt_ff = '';
        foreach ($fl_array as $key => $f) {
            $fl_array[$key] = trim($f);
            $nff = explode('_', $fl_array[$key]);
            $dt_ff1 = ($nff[0] == 'bak') ? $nff[1] : $nff[0];
            $dt_ff1 = substr($dt_ff1, -8);
            if ($dt_ff < $dt_ff1)
                $dt_ff = $dt_ff1;
        }
        $buff = $fl_array;
        $plusieurs = ($dt_ff == $dtj) ? false : true;
// Tentative de modification du dossier en "somedir"
        while ($dt_ff <= $dtj) {
           $mesEntDtj = "\r\nDébut transfert du dossier $dt_ff du ".strtoupper($cbp);
           $rep = "/rbidata/rbi/RBIRecord/cdr/$cbp/normal/bak/$dt_ff/";
            if (ftp_chdir($conn_id, $rep)) {
                $buff2 = ftp_nlist($conn_id, ".");
                $buff1 = array_diff($buff2, $fl_array);
                $bufExist = array_diff($buff2, $buff1);
                if (count($bufExist) && !$plusieurs)
                    $buff = $bufExist;
                
                $ar_file = array();
                foreach ($buff1 as $v) {
                    $type = substr($v, 0, 3);
                    if (isset($maps[$type])) {
                        $localFile = $rep_depo . $maps[$type] . '/' . $v;
                        echo $mesEntDtj."\r\n" . $v;
                        $mesEntDtj = '';
                        if (!ftp_get($conn_id, $localFile, $v, FTP_BINARY, 0))
                            throw("Problème transfert de fichier ftp $cbp");
                        $ar_file[] = $v;
                    }
                    if (count($ar_file) >= 100)
                        break;
                }
                if (count($ar_file)) {
                    $buff = array_merge($buff, $ar_file);
//                file_put_contents($lastFiles1, implode("\n", $buff));
                }
            } else {
                echo "Impossible de changer le dossier\n";
            }
            $dt_ff = date("Ymd", strtotime("+1 days", strtotime($dt_ff)));
        }
        if (count($buff))
            file_put_contents($lastFiles, implode("\n", $buff));
    }

    function listSaveFiles($cbp, $lf) {
        echo "\r\nErreur d\'ouverture du fichier lastFile1";
        global $rep_sauvegarde;
        $tbRep = scandir($rep_sauvegarde);
        foreach ($tbRep as $key => $v) {
            if (!is_dir($rep_sauvegarde . $v) || strpos($v, '20') !== 0) {
                unset($tbRep[$key]);
            }
        }
        rsort($tbRep);
        $tbFiles = array();
        $i = 0;
        while (!count($tbFiles) && isset($tbRep[$i])) {
            $tbFiles = scandir($rep_sauvegarde . $tbRep[$i]);
            unset($tbFiles[0]);
            unset($tbFiles[1]);
            foreach ($tbFiles as $key => $val) {
                if (strpos($val, $cbp) == 0)
                    unset($tbFiles[$key]);
            }
            if (count($tbFiles))
                file_put_contents($lf, implode("\n", $tbFiles));
            $i++;
        }
    }

//******************************** CBP1
    $fl_array = file($lastFiles1);
    if ($fl_array && count($fl_array)) {
       // echo "\r\nDébut CBP1";
        getFiles($fl_array, $lastFiles1, "cbp1");
    } else {
        echo "\r\nErreur d\'ouverture du fichier lastFile1";
        listSaveFiles('_101_', $lastFiles1);
    }


//******************************** CBP2
    $fl_array = file($lastFiles2);
    if ($fl_array && count($fl_array)) {
       // echo "\r\nDébut CBP2";
        getFiles($fl_array, $lastFiles2, "cbp2");
    } else {
        echo "\r\nErreur d\'ouverture du fichier lastFile2";
        listSaveFiles('_102_', $lastFiles2);
    }

    ftp_close($conn_id);
} catch (Exception $e) {
    print_r($e);
}
echo "\r\nFin ftp " . date('Y-m-d H:i:s');
?>