<?php

// 36, 92 Monnaie
// 26, 93 Time
// 25, 95 Data

$conf_bonus_pp = array(
    2 => array(36 => array('montant' => '% C.Principal'), 92 => array('montant' => '% C.Principal'), 26 => array('duree' => '% Durée'), 93 => array('duree' => '% Durée'), 1000 => array('montant' => '% C.Principal')), //Appel Emis
    3 => array(36 => array('montant' => '% C.Principal'), 92 => array('montant' => '% C.Principal'), 26 => array('duree' => '% Durée'), 93 => array('duree' => '% Durée'), 1000 => array('montant' => '% C.Principal')), //Appels reçus
    4 => array(36 => array('montant' => '% C.Principal'), 92 => array('montant' => '% C.Principal'), 1000 => array('montant' => '% C.Principal')), //Sms emis
    5 => array(36 => array('valeur' => '% Valeur'), 92 => array('montant' => '% C.Principal'), 1000 => array('valeur' => '% Valeur')), //Recharge
    6 => array(36 => array('montant' => '% Montant', 'cout' => '% Coût'), 92 => array('montant' => '% C.Principal'), 1000 => array('montant' => '% Montant', 'cout' => '% Coût')), //Transfert
    7 => array(36 => array('montant' => '% C.Principal'), 92 => array('montant' => '% C.Principal'), 25 => array('volume' => '% Volume'), 95 => array('volume' => '% Volume'), 1000 => array('montant' => '% C.Principal')), //Data en méga
    8 => array(36 => array('montant' => '% Coùt'), 92 => array('montant' => '% C.Principal'), 1000 => array('montant' => '% Coùt')), //Service
    12 => array(36 => array('consommation' => '% C.Principal'), 92 => array('montant' => '% C.Principal'), 1000 => array('consommation' => '% C.Principal'))  //Consommation
);

$conf_bonus_pp_cumule = array(
    2 => array(
        36 => array('valeur_total' => '% Mnt Total', 'valeur_onnet' => '% Mnt ONNET', 'valeur_offnet' => '% Mnt OFFNET',
            'valeur_int' => '% Mnt INT', 'valeur_roa' => '% Mnt ROA', 'valeur_pays1' => '% Mnt Mali', 'valeur_pays2' => '% Mnt Senégal',
            'valeur_pays3' => '% Mnt Maroc', 'valeur_pays4' => '% Mnt Angola', 'valeur_pays5' => '% Mnt France'
        ),
        92 => array('valeur_total' => '% Mnt Total', 'valeur_onnet' => '% Mnt ONNET', 'valeur_offnet' => '% Mnt OFFNET',
            'valeur_int' => '% Mnt INT', 'valeur_roa' => '% Mnt ROA', 'valeur_pays1' => '% Mnt Mali', 'valeur_pays2' => '% Mnt Senégal',
            'valeur_pays3' => '% Mnt Maroc', 'valeur_pays4' => '% Mnt Angola', 'valeur_pays5' => '% Mnt France'
        ),
        26 => array('duree_total' => '% Durée Total', 'duree_onnet' => '% Durée ONNET', 'duree_offnet' => '% Durée OFFNET',
            'duree_int' => '% Durée INT', 'duree_roa' => '% Durée ROA', 'duree_pays1' => '% Durée INT', 'duree_pays2' => '% Durée Senégal',
            'duree_pays3' => '% Durée Maroc', 'duree_pays4' => '% Durée Angola', 'duree_pays5' => '% Durée France'
        ),
        93 => array('duree_total' => '% Durée Total', 'duree_onnet' => '% Durée ONNET', 'duree_offnet' => '% Durée OFFNET',
            'duree_int' => '% Durée INT', 'duree_roa' => '% Durée ROA', 'duree_pays1' => '% Durée INT', 'duree_pays2' => '% Durée Senégal',
            'duree_pays3' => '% Durée Maroc', 'duree_pays4' => '% Durée Angola', 'duree_pays5' => '% Durée France'
        ),
        1000 => array('valeur_total' => '% Mnt Total', 'valeur_onnet' => '% Mnt ONNET', 'valeur_offnet' => '% Mnt OFFNET',
            'valeur_int' => '% Mnt INT', 'valeur_roa' => '% Mnt ROA', 'valeur_pays1' => '% Mnt Mali', 'valeur_pays2' => '% Mnt Senégal',
            'valeur_pays3' => '% Mnt Maroc', 'valeur_pays4' => '% Mnt Angola', 'valeur_pays5' => '% Mnt France'
        )
    ), //Appel Emis
    3 => array(
        36 => array('valeur_total' => '% Mnt Total', 'valeur_roa' => '% Mnt ROA'),
        92 => array('valeur_total' => '% Mnt Total', 'valeur_roa' => '% Mnt ROA'),
        1000 => array('valeur_total' => '% Mnt Total', 'valeur_roa' => '% Mnt ROA'),
        26 => array('duree_total' => '% Durée Total', 'duree_onnet' => '% Durée ONNET', 'duree_offnet' => '% Durée OFFNET', 'duree_int' => '% Durée INT', 'duree_roa' => '% Durée ROA'),
        93 => array('duree_total' => '% Durée Total', 'duree_onnet' => '% Durée ONNET', 'duree_offnet' => '% Durée OFFNET', 'duree_int' => '% Durée INT', 'duree_roa' => '% Durée ROA')
    ), //Appels reçus
    4 => array(
        36 => array('valeur_total' => '% Mnt Total', 'valeur_onnet' => '% Mnt ONNET', 'valeur_offnet' => '% Mnt OFFNET', 'valeur_int' => '% Mnt INT', 'valeur_roa' => '% Mnt ROA'),
        92 => array('valeur_total' => '% Mnt Total', 'valeur_onnet' => '% Mnt ONNET', 'valeur_offnet' => '% Mnt OFFNET', 'valeur_int' => '% Mnt INT', 'valeur_roa' => '% Mnt ROA'),
        1000 => array('valeur_total' => '% Mnt Total', 'valeur_onnet' => '% Mnt ONNET', 'valeur_offnet' => '% Mnt OFFNET', 'valeur_int' => '% Mnt INT', 'valeur_roa' => '% Mnt ROA')
    ), //Sms emis
    5 => array(
        36 => array('valeur_total' => '% Mnt Total', 'mechili_total' => '% Mnt Mechili', 'total_total' => '% Mnt Facial', 'valeur_webservice' => '% Mnt WebService',
            'valeur_ussd_total' => '% Mnt USSD', 'valeur_ivr_total' => '% Mnt IVR'),
        1000 => array('valeur_total' => '% Mnt Total', 'mechili_total' => '% Mnt Mechili', 'total_total' => '% Mnt Facial', 'valeur_webservice' => '% Mnt WebService',
            'valeur_ussd_total' => '% Mnt USSD', 'valeur_ivr_total' => '% Mnt IVR')
    ), //Recharge
    6 => array(
        36 => array('valeur_out' => '% Mnt Sortant', 'valeur_in' => '% Mnt Entrant', 'valeur_cout' => '% Coût'),
        92 => array('valeur_out' => '% Mnt Sortant', 'valeur_in' => '% Mnt Entrant', 'valeur_cout' => '% Coût'),
        1000 => array('valeur_out' => '% Mnt Sortant', 'valeur_in' => '% Mnt Entrant', 'valeur_cout' => '% Coût')
    ), //Transfert
    7 => array(
        36 => array('valeur_2000' => '% Mnt Pricipal'),
        1000 => array('valeur_2000' => '% Mnt Pricipal'),
        25 => array('volume_total' => '% Vo Total', 'volume_pays' => '% Vo Pays', 'volume_roa' => '% Vo ROA'),
        95 => array('volume_total' => '% Vo Total', 'volume_pays' => '% Vo Pays', 'volume_roa' => '% Vo ROA')
    ), //Data en méga
    8 => array(
        36 => array('valeur_all' => '% Mnt Total', 'valeur_alldata' => '% Mnt AllData', 'valeur_allsms' => '% Mnt AllSMS', 'valeur_alltime' => '% Mnt AllTIME', 'valeur_allvoix' => '% Mnt AllVOICE'),
        92 => array('valeur_all' => '% Mnt Total', 'valeur_alldata' => '% Mnt AllData', 'valeur_allsms' => '% Mnt AllSMS', 'valeur_alltime' => '% Mnt AllTIME', 'valeur_allvoix' => '% Mnt AllVOICE'),
        1000 => array('valeur_all' => '% Mnt Total', 'valeur_alldata' => '% Mnt AllData', 'valeur_allsms' => '% Mnt AllSMS', 'valeur_alltime' => '% Mnt AllTIME', 'valeur_allvoix' => '% Mnt AllVOICE')
    ), //Service
    12 => array(
        36 => array('total' => '% Mnt Consommé'),
        92 => array('total' => '% Mnt Consommé'),
        1000 => array('total' => '% Mnt Consommé')
    )  //Consommation
);
//print_r($conf_bonus_pp);
?>