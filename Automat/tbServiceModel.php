<?php
require_once 'connection.php';
class Service {
	static function Ajouter(){
		global $connection, $tbAllTables, $CodeService;
		
		$reqCreationService = "CREATE  TABLE  public.data_service_$CodeService (  
		 numero bigint  NOT  NULL ,
		 total_j1 integer DEFAULT  '0',
		 total_j2 integer DEFAULT  '0',
		 total_j3 integer DEFAULT  '0',
		 total_j4 integer DEFAULT  '0',
		 total_j5 integer DEFAULT  '0',
		 total_j6 integer DEFAULT  '0',
		 total_j7 integer DEFAULT  '0',
		 total_j8 integer DEFAULT  '0',
		 total_j9 integer DEFAULT  '0',
		 total_j10 integer DEFAULT  '0',
		 total_j11 integer DEFAULT  '0',
		 total_j12 integer DEFAULT  '0',
		 total_j13 integer DEFAULT  '0',
		 total_j14 integer DEFAULT  '0',
		 total_j15 integer DEFAULT  '0',
		 total_j16 integer DEFAULT  '0',
		 total_j17 integer DEFAULT  '0',
		 total_j18 integer DEFAULT  '0',
		 total_j19 integer DEFAULT  '0',
		 total_j20 integer DEFAULT  '0',
		 total_j21 integer DEFAULT  '0',
		 total_j22 integer DEFAULT  '0',
		 total_j23 integer DEFAULT  '0',
		 total_j24 integer DEFAULT  '0',
		 total_j25 integer DEFAULT  '0',
		 total_j26 integer DEFAULT  '0',
		 total_j27 integer DEFAULT  '0',
		 total_j28 integer DEFAULT  '0',
		 total_j29 integer DEFAULT  '0',
		 total_j30 integer DEFAULT  '0',
		 total_j31 integer DEFAULT  '0',
		 total_j32 integer DEFAULT  '0',
		 total_m1 integer DEFAULT  '0',
		 total_m2 integer DEFAULT  '0',
		 total_m3 integer DEFAULT  '0',
		 total_m4 integer DEFAULT  '0',
		 total_m5 integer DEFAULT  '0',
		 total_m6 integer DEFAULT  '0',
		 total_m7 integer DEFAULT  '0',
		 total_m8 integer DEFAULT  '0',
		 total_m9 integer DEFAULT  '0',
		 total_m10 integer DEFAULT  '0',
		 total_m11 integer DEFAULT  '0',
		 total_m12 integer DEFAULT  '0',
		 total_m13 integer DEFAULT  '0',
		 total_a1 bigint DEFAULT  '0',
		 total_a2 bigint DEFAULT  '0',
		 total_a3 bigint DEFAULT  '0',
		 total_a4 bigint DEFAULT  '0',
		 total_a5 bigint DEFAULT  '0',
		  CONSTRAINT data_service_".$CodeService."_pkey PRIMARY KEY (numero)
		)
		WITH (
		  OIDS=FALSE
		)";
		 
		 try{
			$result = $connection->query($reqCreationService);
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
		$rqInsertAllNum = 'INSERT INTO data_service_'.$CodeService.' (numero) SELECT numero FROM data_attribut';
		 try{
			$result = $connection->query($rqInsertAllNum);
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		
		$tbAllTables[] = 'data_service_'.$CodeService;
	}
}
?>