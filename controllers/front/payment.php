<?php

include_once dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';
include_once dirname(__FILE__).'/../../helper/PaymentMethodHelper.php';
include_once dirname(__FILE__).'/../../helper/FormatHelper.php';
include_once dirname(__FILE__).'/../../domain/PaymentDiscount.php';
include_once dirname(__FILE__).'/../../domain/Document.php';

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
				$cardsAmounts = $this->getAmounts($cardsInstallments, 'DESCONTO_CREDITO');
			}
			if (!empty($TEFsInstallments)) {
				$TEFsAmounts = $this->getAmounts($TEFsInstallments, 'DESCONTO_TEF');
			}
			if (!empty($bankSlipsInstallments)) {
				$bankSlipsAmounts = $this->getAmounts($bankSlipsInstallments, 'DESCONTO_BOLETO');
			}

			$document = new Document($this->context->customer);

			$data = array(
			            'cardsInstallments' => $cardsInstallments,
			            'cardsAmount' => $cardsAmounts['price'],
			            'cardsNoDiscount' => $cardsAmounts['nodiscount'],
			            'cardsPercent' => $cardsAmounts['percentdiscount'],

			            'tefsInstallments' => $TEFsInstallments,
			            'tefsAmount' => $TEFsAmounts['price'],
			            'tefsNoDiscount' => $TEFsAmounts['nodiscount'],
			            'tefsPercent' => $TEFsAmounts['percentdiscount'],

			            'bankSlipsInstallments' => $bankSlipsInstallments,
			            'bankSlipsAmount' => $bankSlipsAmounts['price'],
			            'bankSlipsNoDiscount' => $bankSlipsAmounts['nodiscount'],
			            'bankSlipsPercent' => $bankSlipsAmounts['percentdiscount'],

			            'mesesVencimento' => $this->getMonths(),
			            'anosVencimento' => $this->getYears(),

						'askDocument' => $document->getMode(),
						'isCNPJ' => $document->isCNPJ(),
						'action_post' => $this->context->link->getModuleLink('bcash', 'validation', array(), true)
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
      				'payment_action_url' => $this->context->link->getModuleLink('bcash', 'payment', array(), true)
      			)
  			);

			$this->setTemplate('payment_option_erro.tpl');
    	}

  	}

	private function getAmounts($paymentOption, $paymentType)
	{
		$price = FormatHelper::monetize($paymentOption[0]->installments[0]->amount);
		$amounts = array('nodiscount' => null, 'price' => $price, 'percentdiscount' => null);

		$discount = Configuration::get(self::prefix . $paymentType);

		if (!empty($discount)) {
			$amounts['nodiscount'] = $price;
			$amounts['price'] = $this->applyDiscount($paymentType);
			$amounts['percentdiscount'] = $discount;
		}

		return $amounts;
	}

	private function applyDiscount($paymentType)
	{
		$paymentDiscount = new PaymentDiscount();
		$simulatedPrice = $paymentDiscount->getSimulatedPrice($this->context->cart, $paymentType);
		return FormatHelper::monetize($simulatedPrice);
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
