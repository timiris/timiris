<?php

try {
    $ftp_server = '192.168.3.113';
    $ftp_user_name = 'cheibani';
    $ftp_user_pass = '123456';
    $lastFiles = '/tim_DATA/dump/in/lastFiles.list';
    $rep_depo_local = '/tim_DATA/dump/in/chargement/';
    $rep_depo_distant = '/export/home/cheibani/reporting/';
    $rep_arch = '/tim_arch/dump/in/';
    echo "\r\nDébut ftp " . date('Y-m-d H:i:s');

    function is_ocs_dump($fileName) {
        $spl = explode('_', $fileName);
        $arr_ret = array();
        if (count($spl) != 4)
            return $arr_ret;
        $dt = $spl[0];
        $ocs = $spl[1];
        $sub = $spl[2];
        $rest = explode('.', $spl[3]);
        if (count($rest) == 2) {
            $cbp = $rest[0];
            $ext = $rest[1];
            if (strlen($dt) == 8 && substr($dt, 0, 2) == 20 && $ocs == 'ocs' && $sub == 'subscriber' && $ext == 'list' && ($cbp == 101 || $cbp == 102)) {
                $arr_ret[] = $dt;
                $arr_ret[] = $cbp;
            }
        }
        return $arr_ret;
    }

    $conn_id = ftp_connect($ftp_server);
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
    if ((!$conn_id) || (!$login_result)) {
        die("\r\nEchec de la connexion FTP !");
    }
    if (!is_file($lastFiles))
        die("\r\nErreur : fichier non valide");
    $fl_array = file($lastFiles);
    foreach ($fl_array as $key => $f) {
        $fl_array[$key] = trim($f);
    }
    rsort($fl_array);
    $dt_last_file = '';
    foreach ($fl_array as $key => $f) {
        if (is_ocs_dump($f)) {
            $dt_last_file = substr($f, 0, 8);
            break;
        }
    }
    $buff = array();
    $ft = $fi = 0;
    // Tentative de modification du dossier en "somedir"
    if (ftp_chdir($conn_id, $rep_depo_distant)) {
        $buff2 = ftp_nlist($conn_id, ".");
        $buff1 = array_diff($buff2, $fl_array);
        foreach ($buff1 as $fileName) {
            $arr_ret = is_ocs_dump($fileName);
            if (count($arr_ret) and substr($fileName, 0, 8) > $dt_last_file) {
                $localFile = $rep_depo_local . $fileName;
                echo "\r\nDébut transfert du fichier $fileName";
                if (!ftp_get($conn_id, $localFile, $fileName, FTP_BINARY, 0))
                    throw('Problème transfer de fichier dump in');
                $ft++;
                $dt = $arr_ret[0]; 
                $cbp = $arr_ret[1]; 
                echo "\r\nDébut découpage du fichier $fileName";
                $cmd = "split -dl 50000 --additional-suffix=.list " . $rep_depo_local . $fileName . " " . $rep_depo_local . "din" . $dt . "_" . $cbp . "_";
                exec($cmd);
                rename($rep_depo_local . $fileName, $rep_arch . $fileName);
            }
            else
                $fi++;
        }
        if (count($buff1))
            $buff = array_merge($buff1, $fl_array);

        echo "\r\n" . count($buff1) . " Nv fichiers, $ft transférés, $fi ignorés";
        //file_put_contents($lastFiles1, implode("\n", $buff));
    } else {
        echo "\r\nImpossible de changer le dossier";
    }
    if (count($buff))
        file_put_contents($lastFiles, implode("\n", $buff));
    ftp_close($conn_id);
} catch (Exception $e) {
    print_r($e);
}
echo "\r\nFin ftp " . date('Y-m-d H:i:s');
?>