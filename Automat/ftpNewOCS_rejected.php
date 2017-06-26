<?php

try {
    require 'ftpNewOCS_conf.php';
    $rep_rej = '/tim_DATA/cdrs/rejected/in/';

    $tbFiles = scandir($rep_rej);
    unset($tbFiles[0]);
    unset($tbFiles[1]);
    $tm = date('YmdHis');
    foreach ($tbFiles as $file) {
        $expFile = explode('_', $file);
        if (count($expFile) != 6 || substr($file, -4) != '.unl')
            continue;
        $diff = $df_date = date_diff(date_create($tm), date_create($expFile[4]));
//        if ($diff->h < 6) {
        $dt_ff = substr($expFile[4], 0, 8);
        $rep = "/onip/mep/cdr/CBP/normal/bak/$dt_ff/";
        $localFile = $rep_depo . $expFile[0] . '/' . $file;
        echo "Start ftp of : $localFile\n\r";
        if (ftp_chdir($conn_id, $rep)) {
            if (!ftp_get($conn_id, $localFile, $file, FTP_BINARY, 0)) {
                $dt_ff = date("Ymd", strtotime("+1 days", strtotime($dt_ff)));
                $rep = "/onip/mep/cdr/CBP/normal/bak/$dt_ff/";
                if (ftp_chdir($conn_id, $rep))
                    if (!ftp_get($conn_id, $localFile, $file, FTP_BINARY, 0))
                        throw("Problème transfert du fichier : $file");
                    else {
                        echo "$file is getted\n\r";
                        unlink($rep_rej . $file);
                    }
            } else {
                echo "$file is getted\n\r";
                unlink($rep_rej . $file);
            }
        } else {
            echo "Failed to change directory : $rep\n\r";
        }
//        }
    }
    ftp_close($conn_id);
} catch (Exception $e) {
    print_r($e);
}
echo "\r\nFin ftp " . date('Y-m-d H:i:s');
?>