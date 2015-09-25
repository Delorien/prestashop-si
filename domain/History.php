<?php

class History
{
	private $id_pedido;
	private $id_transacao;
	private $id_status;
	private $status;
	private $pagamento_meio;
	private $parcelas;
	private $valor_original;
	private $valor_loja;
	private $taxa;

	public function __construct($id_pedido, $id_transacao, $id_status, $status, $pagamento_meio, $parcelas = 1, $valor_original = null, $valor_loja = null, $taxa = null)
	{
		$this->id_pedido = $id_pedido;
		$this->id_transacao = $id_transacao;
		$this->id_status = $id_status;
		$this->status = $status;
		$this->pagamento_meio = $pagamento_meio;
		$this->valor_original = $valor_original;
		$this->valor_loja = $valor_loja;
		$this->parcelas = $parcelas;
		$this->taxa = $taxa;
	}

	public function write()
	{
		$tabela = _DB_PREFIX_ . 'bcash_historico';

		$sql = 'INSERT INTO `' . $tabela . '`
					(`id_pedido`,
					`id_transacao`,
					`id_status`,
					`status`,
					`pagamento_meio`';

		if($this->parcelas) {
			$sql .=	',`parcelas`';
		}
		if($this->valor_original) {
			$sql .=	',`valor_original`';
		}
		if($this->valor_loja) {
			$sql .=	',`valor_loja`';
		}
		if($this->taxa) {
			$sql .=	',`taxa`';
		}

		$sql .=	')
				VALUES
					(\'' . $this->id_pedido . '\',
					' . $this->id_transacao . ',
					' . $this->id_status . ',
					\'' . $this->status . '\',
					\'' . $this->pagamento_meio . '\'';

					if($this->parcelas) {
						$sql .= ',' . $this->parcelas;
					}
					if($this->valor_original) {
						$sql .= ',' . $this->valor_original;
					}
					if($this->valor_loja) {
						$sql .= ',' . $this->valor_loja;
					}
					if($this->taxa) {
						$sql .= ',' . $this->taxa;
					}

					$sql .=');';

		$result = Db::getInstance()->Execute($sql);
	}

	static public function getByOrder($orderId)
	{
		if ($orderId == null){
			return false;
		}

		$tabela = _DB_PREFIX_ . 'bcash_historico';

		$sql = 'SELECT * FROM '. $tabela .
				' WHERE id_pedido = ' . $orderId .
				' ORDER BY date_add DESC';

		$results = Db::getInstance()->ExecuteS($sql);

		return $results;
	}

}
