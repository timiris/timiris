<?php
$conf_rec = array();
$pos_msisdn = 4;
$conf_rec['pos']['msisdn'] = 4;
$conf_rec['format']['msisdn'] = 'I';
$pos_msisdn_appele = 5;
$conf_rec['pos']['msisdn_appele'] = 5;
$conf_rec['format']['msisdn_appele'] = 'I';
$pos_profil = 31;
$conf_rec['pos']['profil'] = 31;
$pos_duree = 22;
$conf_rec['pos']['duree'] = 22;
$pos_CptPrincipal = 69;
$conf_rec['pos']['CptPrincipal'] = 69;
$pos_cout = 77;
$conf_rec['pos']['cout'] = 77;
$pos_compteur = 75;
$conf_rec['pos']['compteur'] = 75;
$pos_balance = 78;
$conf_rec['pos']['balance'] = 78;
$pos_heure = 2;
$conf_rec['pos']['heure'] = 2;


$pos_type_appel = 10;
$conf_rec['pos']['type_appel'] = 10;
$conf_rec['val']['type_appel'] = 1;
$pos_appel_renvoi = 11;
$conf_rec['pos']['appel_renvoi'] = 11;
$conf_rec['val']['appel_renvoi'] = 1;
$pos_msc = 12;
$conf_rec['pos']['msc'] = 12;
$conf_rec['val']['msc'] = 00222;
$pos_cellID = 13;
$conf_rec['pos']['cellID'] = 13;
$conf_rec['val']['cellID'] = 60910;
var_dump($conf_rec);
?>