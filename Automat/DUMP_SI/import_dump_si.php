<?php
try{
require_once '../connection.php';
include('config.php');
$cdr = $allRq = array();
$req = "truncate table tmp_dump_si";
$result = $connection->query($req);
echo "Début de chargment du dump SI commercial " . date("Y-m-d H:i:s") .'<br> ';
if( $dir = opendir($rep_chargement) ) {
	while( FALSE !== ($fich = readdir($dir)) ) {
		if ($fich != "." && $fich != ".." ) {
			$fichier = $rep_chargement.'/'.$fich;
			$fp = fopen($fichier,'r');
			$line = 0;
			while(!feof($fp)){
				$li = fgets($fp, 2000);
				if(strlen($li > 20)){
					$line ++;
					$ligne_explode = explode($config['separateur'], $li);
					foreach($config['pos'] as $key=>$val){
						$cdr[$key] = $ligne_explode[$val];
					}
					foreach($config['pfx'] as $key=>$val){
						$cdr[$key] = $val.$cdr[$key];
					}
					$cdr['prenom'] .= ' '.$cdr['nom'];
					$signature = Md5($cdr['numero'].$cdr['prenom'].$cdr['nni'].$cdr['genre']);
					$req = "insert into tmp_dump_si (numero, nom, nni, genre, signature) VALUES 
							('".$cdr['numero']."', '".str_replace("'","''",$cdr['prenom'])."', 
							'".str_replace("'","''",trim($cdr['nni']))."', '".trim($cdr['genre'])."', 
							'".$signature."')";
						$result = $connection->query($req);
				}
			}
			// $nvEmpNom = $rep_sauv.$fich;
			// $fs = rename($fichier, $nvEmpNom);
			fclose($fp);
		}

	}
}
echo"<br>Fin chargment  du dump SI commercial " . date("Y-m-d H:i:s");
}catch(PDOException $e){
	echo $e->getMessage();
	echo $req;
}
?>