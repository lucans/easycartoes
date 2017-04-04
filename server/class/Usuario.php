<?php

	class Usuario extends Dao{

		public $sTable = 'usuarios';
		public $sFields = '';


		function userLogin($coduser, $q, $aDados){

			$sWhere = "WHERE email = '" . $aDados->oUser->email . "' ";

			$aUser = $this->getData($this->sTable, $sWhere, $this->sFields);
			// print_r($aUser[0]);

			if (isset($aUser[0])) {	
				if ($aUser[0]['senha'] == $aDados->oUser->senha) {		
					$_SESSION['user'] = $aUser[0];
					echo json_encode($aUser[0]);
				}		
			} else {
				echo "false";
			}

		}

		function getContatosByUser($coduser, $q, $aDados){

			$sWhere = "WHERE coduser = " . $aDados->oUsuario->coduser;
		
			$aContatos = $this->getData($this->sTable, $sWhere, $this->sFields);

			echo json_encode($aContatos);
		}

		function verificaUserSession(){
			print_r($_SESSION);

			if (isset($_SESSION['user'])) {
				echo json_encode($_SESSION['user']);
			} else { 
				echo 'false'; 
			}

		}

		function cleanUserSession(){
			session_destroy();
		}

	}
?>