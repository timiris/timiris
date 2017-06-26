<?php

try {
    require 'ftpNewOCS_conf.php';
    $rep_sauvegarde = '/tim_Arch/sauvegarde/in/';
    $lastFilesNewOCS = '/tim_DATA/cdrs/chargement/in/lastFilesNewOCS.unl';

    function getFiles($fl_array, $lastFiles, $cbp) {
        global $rep_depo, $conn_id, $maps;
        $dtj = date('Ymd');
        $dt_ff = '';
        foreach ($fl_array as $key => $f) {
            $fl_array[$key] = trim($f);
            $type = substr($fl_array[$key], 0, 3);
            if (isset($maps[$type])) {
                $nff = explode('_', $fl_array[$key]);
                $dt_ff1 = substr($nff[4], 0, 8);
                if ($dt_ff < $dt_ff1)
                    $dt_ff = $dt_ff1;
            }
        }
        $buff = $fl_array;
        $plusieurs = ($dt_ff == $dtj) ? false : true;

        $time_ref = 0;
// Tentative de modification du dossier en "somedir"
        while ($dt_ff <= $dtj) {

            $mesEntDtj = "\r\nDébut transfert du dossier $dt_ff du " . strtoupper($cbp);
            $rep = "/onip/mep/cdr/CBP/normal/bak/$dt_ff/";
            if (ftp_chdir($conn_id, $rep)) {
                $buff2 = ftp_nlist($conn_id, "-t .");
                $buff1 = array_diff($buff2, $fl_array);
                $bufExist = array_diff($buff2, $buff1);
                if (count($bufExist) && !$plusieurs)
                    $buff = $bufExist;

                $ar_file = array();
                foreach ($buff1 as $v) {
                    if ($dt_ff == $dtj) {
                        $time_file = ftp_mdtm($conn_id, $v);
                        if (!$time_ref)
                            $time_ref = $time_file;
                        if ($time_ref - $time_file < 2)
                            continue;
                        if(!ftp_size($conn_id, $v))
                            continue;
                    }
                    
                    $type = substr($v, 0, 3);
                    if (isset($maps[$type])) {
                        $localFile = $rep_depo . $maps[$type] . '/' . $v;
                        echo $mesEntDtj . "\r\n" . $v;
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
    $fl_array = file($lastFilesNewOCS);
    if ($fl_array && count($fl_array)) {
        echo "\r\nDébut CBP1";
        getFiles($fl_array, $lastFilesNewOCS, "cbp1");
    } else {
        echo "\r\nErreur d\'ouverture du fichier lastFileNewOCS";
        listSaveFiles('_101_', $lastFilesNewOCS);
    }

    ftp_close($conn_id);
} catch (Exception $e) {
    print_r($e);
}
echo "\r\nFin ftp " . date('Y-m-d H:i:s');
?>