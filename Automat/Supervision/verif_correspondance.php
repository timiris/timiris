<?php

try {
    $sendMail = false;
    $body = "Bonjour,<br>
             Nous vous informons que le système de supervision de la palteforme TIMIRIS à detecté le(s) alerte(s) suivante(s) :";
    
    require_once '../connection.php';
    require_once '../correspondance.php';
    date_default_timezone_set('Africa/Dakar');
    $dateJ = strtotime(date("Ymd"));
    $dateJP = date("Ymd", strtotime("+1 days", $dateJ));
    $dateMP = date("Ym", strtotime("first day of next month", $dateJ));
    $dateAP = date("Y", strtotime("+1 year", $dateJ));
    $sujetMail = 'Alerte declaration des dates';
    if (!isset($tb_init[$dateJP])) {
        $sendMail = true;
        $body .= "<li>La journée $dateJP n'est pas encore declarée !</li>";
    }
    if (!isset($tb_init[$dateMP])) {
        $sendMail = true;
        $body .= "<li>Le mois $dateMP n'est pas encore declaré !</li>";
    }
    if (!isset($tb_init[$dateAP])) {
        $sendMail = true;
        $body .= "<li>L'année $dateAP n'est pas encore declarée !</li>";
    }
    
    if (!$sendMail) {
        echo "\r\nCorrespondance est bien mise à jours.";
        $sujetMail = 'Alerte initialisation des jours';
        $arr_type = array('msc', 'mgr', 'rec', 'sms', 'vou', 'data');
        foreach ($arr_type as $type) {
            $res = verif_init($tb_init, $type, $dateJP, $dateJP);
            if (count($res)) {
                $sendMail = true;
                $body .= "<li>La date $dateJP n'est pas encore initialisé pour le type de fichier $type !</li>";
            }
        }
        if (!$sendMail)
            echo "\r\nInitialisation est faite avec succès";
    }
    
    $altbody = str_replace('<br>', '\r\n', $body);
    $altbody = str_replace('<li>', '\r\n - ', $altbody);
    $altbody = str_replace('</li>', '\r\n', $altbody);
    if ($sendMail)
        require_once '../mail/envoyer_mail.php';
} catch (PDOException $e) {
    echo "\r\n";
    echo($e->getMessage());
    echo "\r\n";
}
?>