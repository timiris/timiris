<?php
require_once "../fn_security.php";
require_once "../defs.php";
check_session();
if (!isset($_POST["stats"]) || empty($_POST["stats"]))
    exit();
$numero = '222'.(substr($_POST["stats"], -8));
$rf_attr = $rf_loc = $rf_srv = $rf_status = array();
require_once '../conn/connection.php';
require_once '../fn_formatter_date.php';
$req_localisation = "SELECT * FROM ref_wilaya_cellid";
$req_status = "SELECT * FROM ref_etat_ligne_in";
$req_services = "SELECT libelle, code_cmpt FROM ref_compteurs WHERE fk_id_type = ".BNS_SERVICE;
$req_attribut = "SELECT * FROM ref_liste_choix_attribut WHERE attribut = 'flotte' or attribut = 'profil'";

$req_infos = 'SELECT * FROM data_attribut da 
		WHERE da.numero = \'' . $numero . '\' ';
try {
    $result = $connection->query($req_status);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $rf_status[$ligne->id] = $ligne->libelle;
    }

    $result = $connection->query($req_services);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $rf_srv[$ligne->code_cmpt] = $ligne->libelle;
    }

    $result = $connection->query($req_localisation);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $rf_loc[$ligne->cellid] = $ligne->localite . '_' . $ligne->bsc;
    }

    $result = $connection->query($req_attribut);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $rf_attr[$ligne->attribut][$ligne->code] = $ligne->libelle;
    }

    $result = $connection->query($req_infos);
    if ($result->rowCount()) {
        $ligne = $result->fetch(PDO::FETCH_OBJ);

        //******* INFOS commerciales
        $nom = $ligne->nom;
        $points = ($ligne->has_fidelity) ? $ligne->points_fidelite . (($ligne->points_fidelite > 1) ? ' Points' : ' Point').', souscrit le: '.formatter_date($ligne->dt_fidelity):'Non souscrit';

        $date_naissance = $ligne->date_naissance;
        $lieu_naissance = $ligne->lieu_naissance;
        $genre = $ligne->genre;
        $nni = $ligne->nni;
        $dt_active = formatter_date($ligne->dt_active);
        $flotte = isset($rf_attr['flotte'][$ligne->flotte]) ? $rf_attr['flotte'][$ligne->flotte] : (($ligne->flotte) ? "Flotte Code " . $ligne->flotte : "");

        //******* INFOS comportement
        if ($ligne->cellid) {
            $cellid = isset($rf_loc[$ligne->cellid]) ? $rf_loc[$ligne->cellid] : (($ligne->cellid) ? "Cell id : " . $ligne->cellid : "");
            $cellid .= ", le " . formatter_date($ligne->dt_localisation);
        }
        else
            $cellid = "";

        $dt_sms = formatter_date($ligne->dt_sms);
        $dt_sms_recu = formatter_date($ligne->dt_sms_recu);
        $dt_appel_emis_total = formatter_date($ligne->dt_appel_emis_total);
        $dt_appel_recu_total = formatter_date($ligne->dt_appel_recu_total);

        if ($ligne->montant_recharge)
            $montant_recharge = number_format($ligne->montant_recharge / 100, 2, '.', ' ') . " UM , le " . formatter_date($ligne->dt_recharge);
        else
            $montant_recharge = "";

        if ($ligne->service) {
            $service = 'Service : ' . isset($rf_srv[$ligne->service]) ? $rf_srv[$ligne->service] : "Service Code " . $ligne->service;
            $service .= ", le " . formatter_date($ligne->dt_service);
        }
        else
            $service = "";
        $dt_data = formatter_date($ligne->dt_data);

        if ($ligne->montant_transfere_out)
            $montant_transfere_out = "Montant : " . number_format($ligne->montant_transfere_out / 100, 2, '.', ' ') . " UM , le " . formatter_date($ligne->dt_transfert_out);
        else
            $montant_transfere_out = "";

        if ($ligne->montant_transfere_in)
            $montant_transfere_in = "Montant : " . number_format($ligne->montant_transfere_in / 100, 2, '.', ' ') . " UM , le " . formatter_date($ligne->dt_transfert_in);
        else
            $montant_transfere_in = "";

        //******* INFOS Techniques
        if ($ligne->profil) {
            $profil = isset($rf_attr['profil'][$ligne->profil]) ? $rf_attr['profil'][$ligne->profil] : (($ligne->profil) ? "Profil Code " . $ligne->profil : "");
            $profil .= ", le " . formatter_date($ligne->dt_profil);
        }
        else
            $profil = "";

        if ($ligne->balance)
            $balance = number_format($ligne->balance / 100, 2, '.', ' ') . " UM , le " . formatter_date($ligne->dt_balance);
        else
            $balance = "";

        if (is_numeric($ligne->status)) {
            $status = isset($rf_status[$ligne->status]) ? $rf_status[$ligne->status] : (($ligne->status) ? "Status : " . $ligne->status : "");
            if ($ligne->dt_status)
                $status .= ", le " . formatter_date($ligne->dt_status);
        }
        else
            $status = "";

        if ($ligne->imsi)
            $imsi = ($ligne->imsi) . ", le " . formatter_date($ligne->dt_imsi);
        else
            $imsi = "";

        $dt_active_stop = formatter_date($ligne->dt_active_stop);
        $dt_suspend_stop = formatter_date($ligne->dt_suspend_stop);
        $dt_disable_stop = formatter_date($ligne->dt_disable_stop);
        $langue = ($ligne->is_lang_ar == 'true') ? 'Arabe' : 'Français';
        ?>
        <!--
                <div class="animatedtabs">
                    <ul>
                        <li id="b1" class="selected entTab"><p><span>Commerciales</span></p></li>
                        <li id="b2" class="entTab"><p><span>Techniques</span></p></li>
                        <li id="b3" class="entTab"><p><span>Comportement</span></p></li>
                        <li id="b4" class="entTab"><p><span>Statistique</span></p></li>
                        <li id="b5" class="entTab"><p><span>Campagne</span></p></li>
                    </ul>
                </div>
        -->
        <div class="tabsChrome">
            <div id="b1" class="tabChrome active"><div class="tabChrome-box">Commerciales</div></div>
            <div id="b2" class="tabChrome"><div class="tabChrome-box">Techniques</div></div>
            <div id="b3" class="tabChrome"><div class="tabChrome-box">Comportement</div></div>
            <div id="b4" class="tabChrome"><div class="tabChrome-box">Statistique</div></div>
            <div id="b5" class="tabChrome"><div class="tabChrome-box">Campagne</div></div>
        </div>
        
        <div id="contentTabs" class ="divShadow" > 
            <div id="tab1" class="divTab"><?php require "commerciale.php"; ?></div>
            <div id="tab2" class="divTab" style = "display:none;"><?php require "technique.php"; ?></div>
            <div id="tab3" class="divTab" style = "display:none;"><?php require "comportement.php"; ?></div>
            <div id="tab4" class="divTab" style = "display:none;"><?php require "../stats/infos-stats.php"; ?></div>
            <div id="tab5" class="divTab" style = "display:none;"><?php require_once "../campagne/client/liste_campagne_client.php"; ?></div>
        </div>
        <?php
    } else {
        echo "<center><h2>Numéro non trouvé</h2></center>";
        die();
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>