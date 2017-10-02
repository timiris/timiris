<?php

if (!isset($rep))
    $rep = "../";
require_once $rep . "conn/connection.php";
$lib = array();
$lib['idTypeCompteur'] = 'Champ critère ';
$lib['valeurCritere'] = 'Valeur critère ';
$lib['operateur'] = 'Opérateur ';
$lib['idPeriodeFrom'] = 'Période ';
$lib['idPeriodeTo'] = ' au ';
$lib['idUnitePeriodique'] = 'Unité périodique ';
$lib['idFormule'] = ' Formule ';
$lib['untieValeur'] = '';
$lib['natureType'] = '{Nature Trafic : #_#_#}/{Type données : *_*_*}';

$tbOperateur = array();
$tbOperateur['like1'] = 'Commence par';
$tbOperateur['like2'] = 'Se termine par';
$tbOperateur['like3'] = 'Contient';
$tbOperateur['!='] = 'Différant de';
$tbOperateur['='] = 'Egal à';
$tbOperateur['<'] = 'Inferieur de';
$tbOperateur['<='] = 'Inferieur ou égal';
$tbOperateur['not in'] = 'N\'est pas parmis';
$tbOperateur['in'] = 'Parmis';
$tbOperateur['>'] = 'Supérieur à';
$tbOperateur['>='] = 'Supérieur ou égal';

$tbAssocGr = array();
$tbAssocGr['AND'] = 'tous les groupes';
$tbAssocGr['OR'] = 'au moins un groupe';


$tbFormule = array();
$tbFormule['least'] = 'Minimum';
$tbFormule['greatest'] = 'Maximum';
$tbFormule['SUM'] = 'Somme';
$tbFormule['AVG'] = 'Moyenne';

try {
    $libNature = array();
    $req = "SELECT * FROM  ref_nature";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libNature[$ligne->id] = $ligne->libelle;
    }

    $libRefEvent = array();
    $req = "SELECT * FROM  ref_event";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//		$libTypeDonnees[$ligne->id] = $ligne->libelle;
        $libRefEvent[$ligne->type][strtolower($ligne->code)] = $ligne->libelle;
    }
    
    $libTypeDonnees = array();
    $req = "SELECT * FROM  ref_type_donnee";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//		$libTypeDonnees[$ligne->id] = $ligne->libelle;
        $libTypeDonnees[$ligne->id] = array('libelle' => $ligne->libelle, 'unite' => json_decode($ligne->unite, true));
    }
    
    
    $libTypeDonneesEvent = array();
    $req = "SELECT * FROM  ref_type_donnee_event";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
//		$libTypeDonnees[$ligne->id] = $ligne->libelle;
        $libTypeDonneesEvent[$ligne->id] = array('libelle' => $ligne->libelle, 'unite' => json_decode($ligne->unite, true));
    }
    
    $libNatBonus = array();
    $req = "SELECT * FROM  ref_nature_bonus";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libNatBonus[$ligne->id] = array('libelle' => $ligne->libelle, 'unite' => json_decode($ligne->unite, true));
    }
    
    $libTypeBonus = array();
    $req = "SELECT * FROM  ref_type_bonus";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libTypeBonus[$ligne->id] = $ligne->libelle;
    }

    $libCompteur = array();
    $req = "SELECT * FROM  ref_compteurs";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libCompteur[$ligne->fk_id_type . '_' . strtolower($ligne->code_cmpt)] = $ligne->libelle;
//		$libCompteur[$ligne->fk_id_type.'_'.strtolower($ligne->code_cmpt)] = array('libelle'=>$ligne->libelle, 'unite'=>json_decode($ligne->unite, true));
    }

    $libAttSelect = $libCritereAtt = array();
    $req = "SELECT * FROM  ref_attribut";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        if ($ligne->html == 'select')
            $libAttSelect[] = $ligne->code;
        $libCritereAtt[$ligne->code] = $ligne->libelle;
    }
    
    $libAttChoix = array();
    $req = "SELECT * FROM  ref_liste_choix_attribut";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libAttChoix[$ligne->attribut][$ligne->code] = $ligne->libelle;
    }
    
     $libEventSelect = array();
     $res = $connection->query("select code from ref_event where html = 'select'");
     while ($ligne = $res->fetch(PDO::FETCH_OBJ)) {
        $libEventSelect[] = $ligne->code;
    }

    $libCorrespondance = array();
    $req = "SELECT * FROM historique_correspondance ORDER BY type, h_date ";
    $result = $connection->query($req);
    while ($ligne = $result->fetch(PDO::FETCH_OBJ)) {
        $libCorrespondance[$ligne->champ] = $ligne->h_date;
    }
} catch (PDOException $e) {
    $tbRetour['message'] = $e->getMessage();
}


function createTable($tableName, $connection){
    $rq = "CREATE TABLE public.$tableName(j1 integer DEFAULT 0,j2 integer DEFAULT 0,j3 integer DEFAULT 0,j4 integer DEFAULT 0,j5 integer DEFAULT 0,j6 integer DEFAULT 0,j7 integer DEFAULT 0,j8 integer DEFAULT 0,j9 integer DEFAULT 0,j11 integer DEFAULT 0,j12 integer DEFAULT 0,j13 integer DEFAULT 0,j14 integer DEFAULT 0,j15 integer DEFAULT 0,j16 integer DEFAULT 0,j17 integer DEFAULT 0,j18 integer DEFAULT 0,j19 integer DEFAULT 0,j20 integer DEFAULT 0,j21 integer DEFAULT 0,j22 integer DEFAULT 0,j23 integer DEFAULT 0,j24 integer DEFAULT 0,j25 integer DEFAULT 0,j26 integer DEFAULT 0,j27 integer DEFAULT 0,j28 integer DEFAULT 0,j29 integer DEFAULT 0,j30 integer DEFAULT 0,j31 integer DEFAULT 0,j32 integer DEFAULT 0,m1 integer DEFAULT 0,m2 integer DEFAULT 0,m3 integer DEFAULT 0,m4 integer DEFAULT 0,m5 integer DEFAULT 0,m6 integer DEFAULT 0,m7 integer DEFAULT 0,m8 integer DEFAULT 0,m9 integer DEFAULT 0,m10 integer DEFAULT 0,m11 integer DEFAULT 0,m12 integer DEFAULT 0,m13 integer DEFAULT 0,a1 bigint DEFAULT 0,a2 bigint DEFAULT 0,a3 bigint DEFAULT 0,a4 bigint DEFAULT 0,a5 bigint DEFAULT 0,numero character varying(15) NOT NULL,CONSTRAINT ".$tableName."_pkey PRIMARY KEY (numero)) WITH (OIDS=FALSE)";
    $connection->query($rq);
}
?>