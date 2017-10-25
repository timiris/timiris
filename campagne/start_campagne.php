<?php

$rep = '../';
try {
    require_once $rep . "conn/connection.php";
    require_once $rep . "lib/tbLibelle.php";
    require_once $rep . "ciblage/fn/fn_decode_ciblage.php";
    require_once $rep . "ciblage/fn/fn_generateRequete.php";
    require_once $rep . "ciblage/fn/fn_getDateRel.php";
    require_once $rep . "Automat/config_prp.php";
    require_once $rep . "nameTables.php";
    require_once $rep . "defs.php";
    $arr_id_cmp = array();

    // Stop campagnes date fin deja passée
    $rCE = $connection->query('select ac.id, ac.nbr_cible, ac.nbr_gc, kpi.cible, kpi.gc from app_campagne ac
        join app_campagne_kpi kpi on ac.id = kpi.fk_id_campagne and ac.etat = ' . CMP_ENCOURS . "
            and dt_fin <='" . date('Y-m-d H:i') . "' and tbname ='data_consommation_total'");
    while ($lCE = $rCE->fetch(PDO::FETCH_OBJ)) {
        $idCE = $lCE->id;
        $nbrCibleCE = $lCE->nbr_cible;
        $nbrGcCE = $lCE->nbr_gc;
        $cibleCE = $lCE->cible;
        $gcCE = $lCE->gc;
        $rqroi = $connection->query('select sum(valorisation) v_bonus from app_bonus_details abd JOIN app_bonus ab on ab.id = abd.id_bonus and ab.fk_id_campagne = ' . $idCE);
        if ($rqroi->rowCount())
            $v_bonus = $rqroi->fetch(PDO::FETCH_OBJ)->v_bonus;
        else
            $v_bonus = 0;
        $consCible = ($cibleCE / $nbrCibleCE - (($nbrGcCE) ? $gcCE / $nbrGcCE : 0)) * $nbrCibleCE / 100;
        $roi = $consCible - $v_bonus;
        $connection->query("update app_campagne set etat = " . CMP_TERMINEE . ", dt_fin_relle = '" . date('Y-m-d H:i:s') . "', roi = $roi where id = " . $idCE);
    }

    $req = "SELECT id, id_cible, has_wl, type_bonus FROM app_campagne WHERE etat = " . CMP_ATTENTE . " and (dt_lancement is null or dt_lancement <= '" . date('Y-m-d H:i') . "') ORDER BY id";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $newChez = '';
        $connection->query('BEGIN');
        $idCmp = $ligne->id;
        $has_wl = $ligne->has_wl;
        $typeBonus = $ligne->type_bonus;
        echo "Début Start campagne $idCmp\n\r";
        $connection->query("insert into app_campagne_kpi (fk_id_campagne, tbname)
            select distinct $idCmp, tablecode||'_'||lower(code_cmpt) tbname from ref_compteurs cmpt
            JOIN ref_type_donnee td on td.id = cmpt.fk_id_type where tablecode like 'data%' and fk_id_nature not in (1, 11, 13, 14)");
        $arr_id_cmp[] = $idCmp;
        $idCible = $ligne->id_cible;
        if ($idCible) {
            $req_cible = 'select cible, association_group from app_cibles where id =' . $idCible;
            $res_cible = $connection->query($req_cible);
            if ($ligne_cible = $res_cible->fetch(PDO::FETCH_OBJ)) {
                $associationGroupe = $ligne_cible->association_group;
                $tables = json_decode($ligne_cible->cible, true);
                $req_global = generateRequete('cmp', $tables, $associationGroupe);
                // Get number of cible
                echo "Début Calcul cible campagne $idCmp\n\r";
                $req_ins = "select count(*) nbr_cible from ($req_global) tt
                        where tt.numero not in(select numero from app_campagne_exclus where fk_id_campagne = $idCmp)
                        AND tt.numero not in(select numero from app_campagne_cible where fk_id_campagne = $idCmp)";
                // echo "\n\r $req_ins \n\r";
                $res_ins = $connection->query($req_ins);
                // echo "\n\r $req_ins \n\r";
                $li_ins = $res_ins->fetch(PDO::FETCH_OBJ);
                $nbGT = (int) (0.01 * ($li_ins->nbr_cible));
                $nbGT = ($nbGT > 200) ? 200 : $nbGT;
                $nbrCible = $li_ins->nbr_cible - $nbGT;

                echo "Début insert gt campagne $idCmp\n\r";
                $req_ins = "insert into app_campagne_exclus(fk_id_campagne, numero, is_bl)
                    select $idCmp, tt.numero, false from ($req_global) tt
                        where tt.numero not in(select numero from app_campagne_cible where fk_id_campagne = $idCmp)
						AND tt.numero not in(select numero from app_campagne_exclus where fk_id_campagne = $idCmp)
                        order by  random() limit $nbGT";
                $res_ins = $connection->query($req_ins);

                echo "Début insert cible campagne $idCmp\n\r";
                $req_ins = "insert into app_campagne_cible(fk_id_campagne, numero, is_wl)
                    SELECT $idCmp, tt.numero, false FROM ($req_global) tt
                        where tt.numero not in(select numero from app_campagne_cible where fk_id_campagne = $idCmp)
                        and tt.numero not in(select numero from app_campagne_exclus where fk_id_campagne = $idCmp)";
            } else
                throw('Cible incorrecte');
        }
        else {  // Cible tous le parc:  Active+suspend
            // Get number of cible
            $req_ins = "SELECT count(*) nbr_cible FROM data_attribut where status in (1, 2) and profil != 333333
                        AND numero not in(select numero from app_campagne_exclus where fk_id_campagne = $idCmp)
                        AND numero not in(select numero from app_campagne_cible where fk_id_campagne = $idCmp)";
            $res_ins = $connection->query($req_ins);
            $li_ins = $res_ins->fetch(PDO::FETCH_OBJ);
            $nbGT = (int) (0.01 * ($li_ins->nbr_cible));
            $nbGT = ($nbGT > 200) ? 200 : $nbGT;
            $nbrCible = $li_ins->nbr_cible - $nbGT;

            echo "Début insert gt campagne $idCmp\n\r";
            $req_ins = "insert into app_campagne_exclus(fk_id_campagne, numero, is_bl)
                    SELECT $idCmp, numero, false FROM data_attribut where status in (1, 2) and profil != 333333
                        and numero not in(select numero from app_campagne_exclus where fk_id_campagne = $idCmp)
                        order by  random() limit $nbGT";
            $res_ins = $connection->query($req_ins);

            echo "Début insert cible campagne $idCmp\n\r";
            $req_ins = "insert into app_campagne_cible(fk_id_campagne, numero, is_wl)
                SELECT $idCmp, numero, false FROM data_attribut where status in (1, 2) and profil != 333333
                        and numero not in(select numero from app_campagne_cible where fk_id_campagne = $idCmp)
                        and numero not in(select numero from app_campagne_exclus where fk_id_campagne = $idCmp)";
        }

        $res_ins = $connection->query($req_ins);
        $nbrCible = $res_ins->rowCount();
        if ($nbrCible == 0 && !$has_wl) {
            $newStatusCmp = CMP_REJETEE;
            $newChez = ', chez_profil = profil_saisie';
            echo "La campagne $idCmp rejetée automatiquement, car la cible est de 0\n\r";
            $connection->query("insert into app_campagne_wf (fk_id_campagne, dt_action, id_profil, id_user, new_status, commentaire)
                VALUES ($idCmp, '" . date('YmdHis') . "', 0, 0, $newStatusCmp, 'rejet automatique car le nombre de la cible est 0')");
        } else
            $newStatusCmp = CMP_ENCOURS;

        echo "update infos campagne $idCmp\n\r";
        $req_upd = "update app_campagne set nbr_cible = nbr_cible + $nbrCible $newChez, etat = " . $newStatusCmp . ",dt_lancement_reelle= '" . date('Y-m-d H:i:s') . "', nbr_gc = $nbGT  where id = $idCmp";
        $res_upd = $connection->query($req_upd);
        if ($newStatusCmp == CMP_ENCOURS && $typeBonus == 'fidelite') {
            $cmp_lim = array();
            $reqLim = 'select id, cmp_nbr_bonus, cmp_montant_bonus, cmp_nbr_bonus_jr, cmp_montant_bonus_jr, client_nbr_bonus, client_montant_bonus,
                client_nbr_bonus_jr,client_montant_bonus_jr from app_campagne where id = ' . $idCmp;
            $resLim = $connection->query($reqLim);
            while ($li_lim = $resLim->fetch(PDO::FETCH_OBJ)) {
                $cmp_lim[$li_lim->id] = array(
                    'cmp_nbr_bonus' => $li_lim->cmp_nbr_bonus,
                    'cmp_montant_bonus' => $li_lim->cmp_montant_bonus,
                    'cmp_nbr_bonus_jr' => $li_lim->cmp_nbr_bonus_jr,
                    'cmp_montant_bonus_jr' => $li_lim->cmp_montant_bonus_jr,
                    'cmp_total' => $li_lim->cmp_nbr_bonus + $li_lim->cmp_montant_bonus + $li_lim->cmp_nbr_bonus_jr + $li_lim->cmp_montant_bonus_jr,
                    'client_nbr_bonus' => $li_lim->client_nbr_bonus,
                    'client_montant_bonus' => $li_lim->client_montant_bonus,
                    'client_nbr_bonus_jr' => $li_lim->client_nbr_bonus_jr,
                    'client_montant_bonus_jr' => $li_lim->client_montant_bonus_jr,
                    'client_total' => $li_lim->client_nbr_bonus + $li_lim->client_montant_bonus + $li_lim->client_nbr_bonus_jr + $li_lim->client_montant_bonus_jr
                );
            }
            require 'bonus/bonus_directe.php';
        }
        if ($connection->query('COMMIT')) {
            if ($newStatusCmp == CMP_ENCOURS)
                echo "La campagnes numéro $idCmp est lancée avec succès\n\r";
        } else
            throw('COMMIT impossible');
    }
} catch (PDOException $e) {
    $connection->query('ROLLBACK');
    echo $e->getMessage();
    print_r($e);
}
?>