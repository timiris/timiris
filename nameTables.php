<?php
require_once "conn/connection.php";
$tbNameTable = array();
$req = 'SELECT id, fk_id_nature, tablecode from ref_type_donnee';
$result = $connection->query($req);
if ($result->rowCount()) {
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $tbNameTable[$ligne->fk_id_nature . '_' . $ligne->id] = $ligne->tablecode;
    }
}
//$tbNameTable['1_1'] = 'data_attribut';
//$tbNameTable['1_2'] = 'data_attribut';
//$tbNameTable['1_3'] = 'data_attribut';
//
//$tbNameTable['2_4'] = 'data_appel_emis_nombre';
//$tbNameTable['2_5'] = 'data_appel_emis_duree';
//$tbNameTable['2_11'] = 'data_appel_emis_valeur';    // valeur par prefix
//$tbNameTable['2_26'] = 'data_appel_emis_valeur';    // valeur par compteur
//$tbNameTable['2_36'] = 'data_appel_emis_valeur';    // valeur par compteur
//
//$tbNameTable['3_39'] = 'data_appel_recu_nombre';
//$tbNameTable['3_40'] = 'data_appel_recu_duree';
//$tbNameTable['3_50'] = 'data_appel_recu_valeur';
//
//$tbNameTable['4_7'] = 'data_sms_emis_nombre';
//$tbNameTable['4_8'] = 'data_sms_emis_valeur';       // Valeur par prefix
//$tbNameTable['4_14'] = 'data_sms_emis_valeur';      // Valeur par compteur
//$tbNameTable['4_24'] = 'data_sms_emis_valeur';      // Valeur par compteur
//
//$tbNameTable['5_9'] = 'data_recharge_valeur';
//$tbNameTable['5_10'] = 'data_recharge_nombre';
//$tbNameTable['5_27'] = 'data_recharge_valeur_ivr';
//$tbNameTable['5_32'] = 'data_recharge_nombre_ivr';
//$tbNameTable['5_28'] = 'data_recharge_nombre_ussd';
//$tbNameTable['5_29'] = 'data_recharge_valeur_ussd';
//$tbNameTable['5_30'] = 'data_recharge_valeur_total';
//$tbNameTable['5_31'] = 'data_recharge_nombre_total';
//$tbNameTable['5_43'] = 'data_recharge_nombre_mechili';
//$tbNameTable['5_44'] = 'data_recharge_valeur_mechili';
////$tbNameTable['5_45'] = 'data_recharge_webservice_nombre';
////$tbNameTable['5_46'] = 'data_recharge_webservice_valeur';
//
//$tbNameTable['6_12'] = 'data_mgr_nombre';
//$tbNameTable['6_13'] = 'data_mgr_valeur';
//
//$tbNameTable['7_15'] = 'data_data_volume';
//$tbNameTable['7_16'] = 'data_data_valeur';  // Valeur par prefix
//$tbNameTable['7_19'] = 'data_data_valeur';  // Valeur par compteur
//$tbNameTable['7_25'] = 'data_data_valeur';  // Valeur par compteur
//
//$tbNameTable['8_17'] = 'data_service_nombre';
//$tbNameTable['8_18'] = 'data_service_valeur';
//
//$tbNameTable['9_47'] = 'data_sms_recu_nombre';
//$tbNameTable['9_48'] = 'data_sms_recu_valeur';
//
//$tbNameTable['10_20'] = 'data_change_balance_nombre';
//$tbNameTable['10_21'] = 'data_change_balance_valeur';
//
//$tbNameTable['11_22'] = 'data_point_fidelite_nombre';
//$tbNameTable['11_23'] = 'data_point_fidelite_valeur';
//
//$tbNameTable['12_49'] = 'data';
//
//$tbNameTable['13_51'] = 'data_attribut_profil_all';
//$tbNameTable['13_52'] = 'data_attribut_profil_active';
//$tbNameTable['13_53'] = 'data_attribut_profil_suspend';
//$tbNameTable['13_54'] = 'data_attribut_profil_disable';
//$tbNameTable['13_55'] = 'data_attribut_profil_pool';
//$tbNameTable['13_56'] = 'data_attribut_profil_idle';
?>