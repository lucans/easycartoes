<?php

	class Cartao extends Dao {

		public $sTable = 'cartoes';
		public $sFields = '';


		public function getPreco($tipo, $tipo_preco){
									
			$sTable = "precos";
			$sWhere = " WHERE tipo = '$tipo'";		

			$aTipo = $this->getData($sTable, $sWhere, $this->sFields);			

			return $aTipo[0][$tipo_preco];
		}


		public function getCartoes($coduser, $q, $aDados){			

			$sTable = "cartoes c LEFT JOIN cartoes_tipos ct ON c.codtipo = ct.codtipo";

			$sWhere = ($q != '' ? "WHERE tamanho = '$q' " : "") . "ORDER BY RAND()" ;

			$aCartoes = $this->getData($sTable, $sWhere, $this->sFields,'');
		
			echo json_encode($aCartoes);

		}

		public function getCartoesRecebidos($coduser, $q, $aDados){
			
			$sTable = "cartoes c INNER JOIN envios e ON c.codmodelo = e.codmodelo";
			$sWhere = "WHERE coduser_receiver = '$coduser' ORDER BY dtenvio";

			$aCartoes = $this->getData($sTable, $sWhere, $this->sFields);
		
			echo json_encode($aCartoes);

		}




	}

?>