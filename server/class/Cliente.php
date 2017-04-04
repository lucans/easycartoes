<?php

	class Cliente extends Dao {

		public $sTable = 'clientes';
		public $sFields = '';
		public $sAtivo = "AND ativo = 'S'";


		public function getClientes($user, $q, $aDados){
			
			$sFields = " *, DATEDIFF(CURDATE(), ultima_compra) AS ultima_compra_dias ";

			$sWhere = "WHERE ativo = 'S' ORDER BY ultima_compra_dias DESC";

			$aClientes = $this->getData($this->sTable, $sWhere, $sFields);


			foreach ($aClientes as $key => $cliente) {				
				$aClientes[$key]['total_pedidos_pendentes'] = self::getFiadoByCliente($cliente["codcliente"]);					
			}

	
			echo json_encode($aClientes);

		}


		public function getFiadoByCliente($codcliente){
			
			$sTable = "pedidos";

			$sFields = "codpedido, COUNT(*) AS total";

			$sWhere = " WHERE entrega = 'entregue' AND pagamento = 'fiado' AND codcliente = '$codcliente' ";

			$aTotal = $this->getData($sTable, $sWhere, $sFields);					

			return $aTotal[0]["total"];

		}

		public function getClientesByString($user, $q, $aDados){
		
			$sWhere = " WHERE CONCAT(nome, endereco, telefone) LIKE '%" . $aDados->oPedido->nome . "%' AND ativo = 'S' LIMIT 3";

			$aClientes = $this->getData($this->sTable, $sWhere, $this->sFields);					

			foreach ($aClientes as $key => $cliente) {				
				$aClientes[$key]['total_pedidos_pendentes'] = self::getFiadoByCliente($cliente["codcliente"]);					
			}

			echo json_encode($aClientes);

		}


		public function getOnePedido($user, $codpedido){

			$sWhere = "WHERE codpedido = '$codpedido' AND coduser = '$user' " . $this->sAtivo;
			$aPedido = $this->getData($this->sTable, $sWhere, $this->sFields);
			echo json_encode($aPedido);

		}
		
		public function updateCliente($user, $q, $aDados){

			$sTable = "clientes";

			$ultima_compra = implode('-', array_reverse(explode('/', $aDados->oPedido->dia)));

			$sSet = "SET ultima_compra = '$ultima_compra'";	

			$sWhere = "WHERE codcliente = '" . $aDados->oPedido->codcliente . "'";
			
			$this->updateData($sTable, $sWhere, $sSet);

		}	

		public function insertCliente($user, $q, $aDados){
			
			$sTable = "clientes";

			$ultima_compra = implode('-', array_reverse(explode('/', $aDados->oPedido->dia)));

			$sSet = "SET nome = '" . utf8_decode($aDados->oPedido->nome) . "',
					 	 endereco = '" . utf8_decode($aDados->oPedido->endereco) . "',
					 	 telefone = '" . $aDados->oPedido->telefone . "',
					 	 ultima_compra = '$ultima_compra'";	
			
			$this->insertData($sTable, $sSet);

			$iCodCliente = $this->getData($sTable, "", "nome, MAX(codcliente) as codcliente");

			return $iCodCliente[0]["codcliente"];
		}	


		public function deleteCliente($user, $q, $aDados){
			$sWhere = "WHERE codcliente = '" . $aDados->oCliente->codcliente . "'";

			$this->deleteData($this->sTable, $sWhere);			
			echo json_encode(array('msg' => 'true'));
		
		}

		public function changePagamento($user, $q, $aDados){

			$sDataPagamento = $aDados->oPedido->pagamento == 'fiado' ? 'CURDATE()' : "'0000-00-00'";


			$sSet = " SET pagamento = IF(pagamento = 'fiado','pago','fiado'), dtpagamento = $sDataPagamento ";

			$sWhere = "WHERE codpedido = '" . $aDados->oPedido->codpedido . "' ";
			
			$this->updateData($this->sTable, $sWhere, $sSet);

		}	



	}

?>