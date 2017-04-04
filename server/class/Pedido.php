<?php

	class Pedido extends Dao {

		public $sTable = 'pedidos';
		public $sFields = '';
		public $sAtivo = "AND ativo = 'S'";


		public function getPedidos($user){

			$sFields = " dia, CASE DAYNAME(dia)
							WHEN 'Sunday' THEN 'Domingo'
							WHEN 'Monday' THEN 'Segunda-Feira'
							WHEN 'Tuesday' THEN 'Terça-Feira'
							WHEN 'Wednesday' THEN 'Quarta-Feira'
							WHEN 'Thursday ' THEN 'Quinta-Feira'
							WHEN 'Friday' THEN 'Sexta-Feira'
							WHEN 'Saturday' THEN 'Sábado' 
						END AS dia_semana ";			
		
			$sWhere = " GROUP BY dia ORDER BY dia DESC LIMIT 30";

			$Dias = $this->getData($this->sTable, $sWhere, $sFields);

			$aPedidos = array();

			foreach ($Dias as $key => $Dia) {		

				$Dia['pedidos'] = self::getPedidosByDia($Dia['dia']);		

				array_push($aPedidos, $Dia);

			}

			echo json_encode($aPedidos);

		}

		public function getPedidosByDia($dia){

			$sTable = " pedidos p INNER JOIN clientes c ON p.codcliente = c.codcliente";

			$sWhere = " WHERE dia = '$dia' ";

			$aPedidos = $this->getData($sTable, $sWhere, $this->sFields);

			foreach ($aPedidos as $key => $pedido) {				
				$aPedidos[$key]['previsao'] = toHtmlFormat($pedido['previsao']);					
			}

			return $aPedidos;

		}




		public function getOnePedido($user, $codpedido){

			$sWhere = "WHERE codpedido = '$codpedido' AND coduser = '$user' " . $this->sAtivo;
			$aPedido = $this->getData($this->sTable, $sWhere, $this->sFields);
			echo json_encode($aPedido);

		}
		
		public function updatePedido($user, $q, $aDados){

			$aDados->oPedido->previsao = implode('-', array_reverse(explode('/', $aDados->oPedido->previsao)));			

			if (isset($aDados->oPedido->previsao)) {
				$aDados->oPedido->dtpagamento = '0000-00-00';
			}

			$sSet = buildSet($aDados);

			$sWhere = "WHERE codpedido = '" . $aDados->oPedido->codpedido . "' AND coduser = '$user' ";
			
			$this->updateData($this->sTable, $sWhere, $sSet);

		}	

		public function insertPedido($user, $q, $aDados){
			
			$Cliente = new Cliente();

			if (isset($aDados->oPedido->codcliente)) {
				$Cliente->updateCliente("1", "", $aDados);
			} else {
				$aDados->oPedido->codcliente = $Cliente->insertCliente("1", "", $aDados);				
			}

			$Generic = new Generic();
			$Generic->getPreco($aDados->oPedido->tipo, "preco_venda");

			$aDados->oPedido->preco = $Generic->getPreco($aDados->oPedido->tipo, "preco_venda");

			$aDados->oPedido->dia = implode('-', array_reverse(explode('/', $aDados->oPedido->dia)));

			$aDados->oPedido->dtcadastro = date("Y-m-d H:i:s");

			$aDados->oPedido->coduser = $user;

			unset($aDados->oPedido->nome);
			unset($aDados->oPedido->endereco);
			unset($aDados->oPedido->telefone);			

			$sSet = buildSet($aDados);		
			
			$this->insertData($this->sTable, $sSet);

		}	

		public function deletePedido($user, $q, $aDados){

			$sWhere = "WHERE codpedido = '" . $aDados->oPedido->codpedido . "'";

			$sWhere = "WHERE codpedido = '" . $aDados->oPedido->codpedido . "'";
			$this->deleteData($this->sTable, $sWhere);			
			echo json_encode(array('msg' => 'true'));
		
		}

		public function changeEntrega($user, $q, $aDados){

			$sDataPagamento = $aDados->oPedido->entrega == 'pendente' ? 'CURDATE()' : "'0000-00-00'";

			$sSet = " SET entrega = IF(entrega = 'pendente','entregue','pendente'), dtpagamento = $sDataPagamento ";
			
			$sWhere = "WHERE codpedido = '" . $aDados->oPedido->codpedido . "' ";
			
			$this->updateData($this->sTable, $sWhere, $sSet);

		}	

		public function changePagamento($user, $q, $aDados){

			$sDataPagamento = $aDados->oPedido->pagamento == 'fiado' ? 'CURDATE()' : "'0000-00-00'";


			$sSet = " SET pagamento = IF(pagamento = 'fiado','pago','fiado'), dtpagamento = $sDataPagamento ";

			$sWhere = "WHERE codpedido = '" . $aDados->oPedido->codpedido . "' ";
			
			$this->updateData($this->sTable, $sWhere, $sSet);

		}	



		public function getPedidosByMesAno($user, $q, $aDados){
				
			$aRelatorio = array();


			// Primeira
			$aRelatorio["primeira"]["parte"] = "Primeira";
			$aRelatorio["primeira"]["intervalo"] = "01 a 10";


			// Pedidos
			$sTable = "pedidos p INNER JOIN clientes c ON p.codcliente = c.codcliente";
			$sFields = "*, c.nome, c.endereco, c.telefone";

			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-01' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-10' AND pagamento = 'pago' ORDER BY dia ASC";
			$aRelatorio["primeira"]["pedidos_pagos"] = $this->getData($sTable, $sWhere, $sFields);

			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-01' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-10' AND pagamento = 'fiado' ORDER BY dia ASC";
			$aRelatorio["primeira"]["pedidos_fiado"] = $this->getData($sTable, $sWhere, $sFields);


			// Totais
			$sTable = "pedidos p 
						INNER JOIN precos pr
						ON pr.tipo = p.tipo
						";

			$sFields = "p.tipo, p.preco, SUM(p.qtd) AS total_qtd, SUM(p.qtd * p.preco) AS total_valor";
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-01' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-10' AND pagamento = 'pago' GROUP BY p.tipo";
			$aRelatorio["primeira"]["totais_pagos"] = $this->getData($sTable, $sWhere, $sFields);
			
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-01' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-10' AND pagamento = 'fiado' GROUP BY p.tipo";
			$aRelatorio["primeira"]["totais_fiado"] = $this->getData($sTable, $sWhere, $sFields);
			

			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-01' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-10' GROUP BY p.tipo";
			$aRelatorio["primeira"]["total_geral"] = $this->getData($sTable, $sWhere, $sFields);
			





			// Segunda
			$aRelatorio["segunda"]["parte"] = "Segunda";
			$aRelatorio["segunda"]["intervalo"] = "11 a 20";

			// Pedidos
			$sTable = "pedidos p INNER JOIN clientes c ON p.codcliente = c.codcliente";
			$sFields = " *, c.nome, c.endereco, c.telefone";
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-11' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-20' AND pagamento = 'pago' ORDER BY dia ASC";
			$aRelatorio["segunda"]["pedidos_pagos"] = $this->getData($sTable, $sWhere, $sFields);

			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-11' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-20' AND pagamento = 'fiado' ORDER BY dia ASC";
			$aRelatorio["segunda"]["pedidos_fiado"] = $this->getData($sTable, $sWhere, $sFields);


			// Totais
			$sTable = "pedidos p 
						INNER JOIN precos pr
						ON pr.tipo = p.tipo
						";

			$sFields = "p.tipo, p.preco, SUM(p.qtd) AS total_qtd, SUM(p.qtd * p.preco) AS total_valor";
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-11' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-20' AND pagamento = 'pago' GROUP BY p.tipo";
			$aRelatorio["segunda"]["totais_pagos"] = $this->getData($sTable, $sWhere, $sFields);
			
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-11' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-20' AND pagamento = 'fiado' GROUP BY p.tipo";
			$aRelatorio["segunda"]["totais_fiado"] = $this->getData($sTable, $sWhere, $sFields);

			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-11' AND '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-20' GROUP BY p.tipo";
			$aRelatorio["segunda"]["total_geral"] = $this->getData($sTable, $sWhere, $sFields);





			// Terceira
			$aRelatorio["terceira"]["parte"] = "Terceira";
			$aRelatorio["terceira"]["intervalo"] = "21 ao fim";

			// Pedidos
			$sTable = "pedidos p INNER JOIN clientes c ON p.codcliente = c.codcliente";
			$sFields = " *, c.nome, c.endereco, c.telefone";
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-21' AND LAST_DAY('" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-15') AND pagamento = 'pago' ORDER BY dia ASC";
			$aRelatorio["terceira"]["pedidos_pagos"] = $this->getData($sTable, $sWhere, $sFields);
			
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-21' AND LAST_DAY('" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-15') AND pagamento = 'fiado' ORDER BY dia ASC";
			$aRelatorio["terceira"]["pedidos_fiado"] = $this->getData($sTable, $sWhere, $sFields);


			// Totais
			$sTable = "pedidos p 
						INNER JOIN precos pr
						ON pr.tipo = p.tipo
						";

			$sFields = "p.tipo, p.preco, SUM(p.qtd) AS total_qtd, SUM(p.qtd * p.preco) AS total_valor";
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-21' AND LAST_DAY('" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-15') AND pagamento = 'pago' GROUP BY p.tipo";
			$aRelatorio["terceira"]["totais_pagos"] = $this->getData($sTable, $sWhere, $sFields);
			
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-21' AND LAST_DAY('" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-15') AND pagamento = 'fiado' GROUP BY p.tipo";
			$aRelatorio["terceira"]["totais_fiado"] = $this->getData($sTable, $sWhere, $sFields);
			
			$sWhere = " WHERE dia BETWEEN '" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-21' AND LAST_DAY('" . $aDados->oRelatorio->ano . "-" . $aDados->oRelatorio->mes . "-15') GROUP BY p.tipo";
			$aRelatorio["terceira"]["total_geral"] = $this->getData($sTable, $sWhere, $sFields);
	

			echo json_encode($aRelatorio);

		}

	}

?>