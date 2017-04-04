<?php

	class Generic extends Dao {

		public $sTable = 'envios';
		public $sFields = '';


		public function insertEnvio($user, $q, $aDados){			
			
			$sSet = buildSet($aDados);

			$this->insertData($this->sTable, $sSet);			

			echo json_encode(['msg' => 'true']);
		}	


	}

?>