<?php

class Document
{
	const prefix = 'BCASH_';
	private $customer = null;
	private $isCNPJ = null;

	public function __construct($customer)
	{
		$this->customer = $customer;
	}

	public function getMode()
	{
		$docConfig = Configuration::get(self::prefix . 'CAMPO_CPF');

		if ($docConfig != 'exibir') {

			$document = $this->find();
			if ($document) {
				if(!$this->isCNPJ($document) ) {
					return 'specified';
				}
			}
		}
		return 'exibir';
	}

	public function find()
	{
		$tabela = _DB_PREFIX_ . Configuration::get(self::prefix.'TABLE_CPF');
		$coluna = Configuration::get(self::prefix.'CAMPO_CPF_SELECT');
		$where = Configuration::get(self::prefix.'WHERE_CPF');

		$sql = 'SELECT ' . $coluna . ' FROM ' . $tabela . 
				' WHERE ' . $where . ' = ' . $this->customer->id;
		$result = Db::getInstance()->getValue($sql);

		return $result;
	}

	public function isCNPJ($document = null)
	{
		if ( is_null($document) ) {
			if (!is_null($this->isCNPJ)) {
				return $this->isCNPJ;
			}
			return false;
		}

		$this->isCNPJ = false;

		$document = self::sanitize($document);
		if (isset($document[11])) {
			$this->isCNPJ = true;
		}

		return $this->isCNPJ;
	}

	public static function sanitize ($document)
	{
		return preg_replace("/[^0-9]/", "", $document);
	}

}
