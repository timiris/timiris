<?php

try {
    $ftp_server = '192.168.3.113';
    $ftp_user_name = 'cheibani';
    $ftp_user_pass = '123456';
    $rep_sauvegarde = '/tim_arch/dump/fidelity/';
    $rep_dist = '/export/home/cheibani/TIMIRIS/';
    $tbFiles = scandir($rep_sauvegarde);
    unset($tbFiles[0]);
    unset($tbFiles[1]);

    rsort($tbFiles);
    $i = 0;
    foreach ($tbFiles as $k => $file) {
        if (substr($file, -4) != '.txt') {
            $i++;
            unset($tbFiles[$k]);
            if ($i > 10) {
                $fichier = $rep_sauvegarde . $file;
                unlink($fichier);
            }
        }
    }
    if (count($tbFiles)) {
        $conn_id = ftp_connect($ftp_server);
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

        if ((!$conn_id) || (!$login_result)) {
            die("Echec de la connexion FTP !");
        }
        ftp_pasv($conn_id, true);
        if (ftp_chdir($conn_id, $rep_dist)) {
            foreach ($tbFiles as $file) {
                $remote_file = $rep_dist . $file;
                $local_file = $rep_sauvegarde . $file;
                if (ftp_put($conn_id, $remote_file, $local_file, FTP_ASCII)) {
                    $cmd = "gzip $local_file";
                    exec($cmd);
                }
            }
        }
        ftp_close($conn_id);
    }
} catch (Exception $e) {
    print_r($e);
}
?>