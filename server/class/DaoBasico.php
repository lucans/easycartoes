<?php
	class Dao{

		private $link;

		public function __construct(){	
			if ($_SERVER['DOCUMENT_ROOT'] == 'C:/Users/Lucas/Documents/GitHub') {				
			    $this->link = mysqli_connect("192.168.10.20","root","proxy","db_cartonline");
			} else if ($_SERVER['DOCUMENT_ROOT'] == 'C:/xampp/htdocs') {
			    $this->link = mysqli_connect("localhost","root","","db_cartonline");
			} 
		}


		public function getData($sTable, $sWhere, $sFields, $opt = ''){
			
			$sWhere = utf8_decode($sWhere);
			$sFields = utf8_decode($sFields);

			$sFields = empty($sFields) ? '*' : $sFields;			

			$sQuery = "SELECT $sFields FROM $sTable $sWhere";		


			if ($opt == 'morre') {
				die($sQuery);
			}

			$oStmt = mysqli_query($this->link, $sQuery); 

			$aResult = array();				

			while($oResult = mysqli_fetch_assoc($oStmt)){
				array_push($aResult, ArrayEncode($oResult));
			}
			
			$aResult = $aResult;
			
			unset($sWhere);
			
			return $aResult;

		}		


		public function insertData($sTable, $sSet, $opt = ''){

			$sQuery = "INSERT INTO $sTable $sSet";

			if ($opt == 'morre') {
				die($sQuery);
			}

			mysqli_query($this->link, $sQuery); 
			unset($sWhere);
			
		}		

		


		public function updateData($sTable, $sWhere, $sSet){

			$sQuery = "UPDATE $sTable $sSet $sWhere";

			// die($sQuery);

			mysqli_query($this->link, $sQuery); 
			unset($sWhere);
			
		}		

		public function deleteData($sTable, $sWhere){

			if ($sTable == 'clientes') {
				$sQuery = "UPDATE $sTable SET ativo = IF(ativo = 'S', 'N', 'S') $sWhere";
			} else {
				$sQuery = "DELETE FROM $sTable $sWhere";			
			}

			mysqli_query($this->link, $sQuery); 

			unset($sWhere);
			
		}

		public function switchTarefa($sTable, $sWhere){

			$sQuery = "UPDATE $sTable SET valor = IF(valor = '1', '0', '1') $sWhere";			
			mysqli_query($this->link, $sQuery); 
			unset($sWhere);
			
		}

	}