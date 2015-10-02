<?php

include_once dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';
include_once dirname(__FILE__).'/../../helper/PaymentMethodHelper.php';
include_once dirname(__FILE__).'/../../helper/FormatHelper.php';

use Bcash\Service\Installments;
use Bcash\Exception\ValidationException;
use Bcash\Exception\ConnectionException;

class BcashPaymentModuleFrontController extends ModuleFrontController
{
	const prefix = 'BCASH_';

	public $display_column_left = false;
	private $tentativas = 0; 

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
  	{
	   	parent::initContent();

		if (empty($this->context->cart->id)) {
			Tools::redirect('index.php?controller=order&step=1');
		}

		$installments = $this->getInstallments();

		if(!$installments === false) {

			$cardsInstallments = $this->getCardInstallments($installments->paymentTypes);
			$TEFsInstallments = $this->getTEFSInstallments($installments->paymentTypes);
			$bankSlipsInstallments = $this->getBankSlipInstallments($installments->paymentTypes);

			$cardsAmounts = null;
			$TEFsAmounts = null;
			$bankSlipsAmounts = null;

			if (!empty($cardsInstallments)) {
				$cardsAmounts = $this->getAmounts($cardsInstallments, Configuration::get(self::prefix . 'DESCONTO_CREDITO'));
			}
			if (!empty($TEFsInstallments)) {
				$TEFsAmounts = $this->getAmounts($TEFsInstallments, Configuration::get(self::prefix . 'DESCONTO_TEF'));
			}
			if (!empty($bankSlipsInstallments)) {
				$bankSlipsAmounts = $this->getAmounts($bankSlipsInstallments, Configuration::get(self::prefix . 'DESCONTO_BOLETO'));
			}

			$data = array(
			            'cardsInstallments' => $cardsInstallments,
			            'cardsAmount' => $cardsAmounts['price'],
			            'cardsNoDiscount' => $cardsAmounts['nodiscount'],

			            'tefsInstallments' => $TEFsInstallments,
			            'tefsAmount' => $TEFsAmounts['price'],
			            'tefsNoDiscount' => $TEFsAmounts['nodiscount'],

			            'bankSlipsInstallments' => $bankSlipsInstallments,
			            'bankSlipsAmount' => $bankSlipsAmounts['price'],
			            'bankSlipsNoDiscount' => $bankSlipsAmounts['nodiscount'],

			            'mesesVencimento' => $this->getMonths(),
			            'anosVencimento' => $this->getYears(),

						'campo_cpf' => $this->getCpfMode(),
						'action_post' => $this->context->link->getModuleLink('bcash', 'validation', [], true)
					);

			$erros_messages = array();

			if (Tools::getValue('retentativa')) {
				foreach (Tools::getValue('b_errors') as $erro) {
					array_push($erros_messages, urldecode($erro['description']));
				}
			}

			$data['b_erros_messages'] = $erros_messages;

			$this->context->smarty->assign($data);

	    	$this->setTemplate('payment.tpl');
    	}else {
			$this->context->smarty->assign(
				array(
      				'payment_action_url' => $this->context->link->getModuleLink('bcash', 'payment', [], true)
      			)
  			);

			$this->setTemplate('payment_option_erro.tpl');
    	}

  	}

	private function getCpfMode()
	{
		$campoCPF = Configuration::get(self::prefix . 'CAMPO_CPF');

		if ( ($campoCPF != 'exibir') && ($this->isCpfOnBase()) ) {
			return 'specified';
		}

		return 'exibir';
	}

	private function isCpfOnBase() 
	{
		$tabela = _DB_PREFIX_ . Configuration::get(self::prefix.'TABLE_CPF');
		$coluna = Configuration::get(self::prefix.'CAMPO_CPF_SELECT');
		$where = Configuration::get(self::prefix.'WHERE_CPF');

		$sql = 'SELECT ' . $coluna . ' FROM ' . $tabela . 
				' WHERE ' . $where . ' = ' . $this->context->customer->id;
		$result = Db::getInstance()->getValue($sql);
		return $result;
	}

	private function getAmounts($paymentOption, $discount)
	{
		$price = FormatHelper::monetize($paymentOption[0]->installments[0]->amount);
		$amounts = array('nodiscount' => null, 'price' => $price);

		if (!empty($discount)) {
			$amounts['nodiscount'] = $price;
			$amounts['price'] = $this->applyDiscount($price, $discount);
		}

		return $amounts;
	}

	private function applyDiscount($price, $discount)
	{
		$newPrice = $price - FormatHelper::monetize((($price * $discount) / 100));
		return FormatHelper::monetize($newPrice);
	}

	/**
     * Set default medias for this controller
     */
    public function setMedia() {
        parent::setMedia();
        // Add you CSS and JS, 
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'bcash/resources/css/bcash_payment.css', 'all');
		$this->context->controller->addCSS(_PS_MODULE_DIR_ . 'bcash/resources/css/progress_bar.css', 'all');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'bcash/resources/js/jquery.validate.min.js', 'all');
		$this->context->controller->addJS(_PS_MODULE_DIR_ . 'bcash/resources/js/payment-form-validator.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'bcash/resources/js/bcash_payment.js');
		$this->context->controller->addJS(_PS_MODULE_DIR_ . 'bcash/resources/js/progress_bar.js');
    }

	private function getMonths() {
		return array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
	}

	private function getYears() {
		$years = array();

		for($x = date("Y"); $x <= date("Y") + 11; $x++) {
			array_push($years, '' . $x);
		}

		return $years;
	}

	private function getInstallments() {
		$email = Configuration::get(self::prefix . 'EMAIL');
		$token =  Configuration::get(self::prefix . 'TOKEN');

		$amount = $this->context->cart->getOrderTotal(true, Cart::BOTH);

		$installments = new Installments($email, $token);
		$installments->enableSandBox(Configuration::get(self::prefix . 'SANDBOX'));

		$response = null;

		try {
 			$response = $installments->calculate($amount);

		} catch (ValidationException $e) {
			return $this->retryInstallments();
		} catch (ConnectionException $e) {
		 	return $this->retryInstallments();
		}
		return $response;
	}

	private function retryInstallments()
	{
		if ($this->tentativas < 5) {
			$this->tentativas++;
			$this->getInstallments();
		}else {
			return false;
		}
	}

	private function getCardInstallments($installments)
	{
		foreach ($installments as $key => $value) {
			if($value->name == "card"){
				return $this->formatInstallments($value->paymentMethods, true);
			}
		}
	}

	private function getTEFSInstallments($installments)
	{
		foreach ($installments as $key => $value) {
			if($value->name == "transferencia"){
				return $this->formatInstallments($value->paymentMethods);
			}
		}
	}

	private function getBankSlipInstallments($installments)
	{
		foreach ($installments as $key => $value) {
			if($value->name == "boleto"){
				return $this->formatInstallments($value->paymentMethods);
			}
		}
	}

	private function formatInstallments($paymentMethods, $installmentAmount = false) {
		foreach ($paymentMethods as $paymentMethod) {
			foreach ($paymentMethod->installments as $installment) {
				$installment->amount = FormatHelper::monetize($installment->amount);
				if ($installmentAmount) {
					$installment->installmentAmount = FormatHelper::monetize($installment->installmentAmount);
				}
			}
		}
		return $paymentMethods;
	}

}
