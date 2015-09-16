<?php

include_once dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';
include_once dirname(__FILE__).'/../../helper/PaymentMethodHelper.php';

use Bcash\Service\Installments;
use Bcash\Exception\ValidationException;
use Bcash\Exception\ConnectionException;

class BcashPaymentModuleFrontController extends ModuleFrontController
{
	const prefix = 'BCASH_';

	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
  	{
	   	parent::initContent();

		$paymentMethodHelper = new PaymentMethodHelper();
		$cards = $paymentMethodHelper->getPaymentMethods()['CARD'];
		$tefs = $paymentMethodHelper->getPaymentMethods()['ONLINE_TRANSFER'];
		$bankSlips = $paymentMethodHelper->getPaymentMethods()['BANKSLIP'];

		$installments = $this->getInstallments();
		$cardsInstallments = $this->getCardInstallments($installments->paymentTypes);
		$TEFsInstallments = $this->getTEFSInstallments($installments->paymentTypes);
		$bankSlipsInstallments = $this->getBankSlipInstallments($installments->paymentTypes);


		$this->context->smarty->assign(
	        array(
	            'cards' => $cards,
	            'cardsInstallments' => $cardsInstallments,
	            'cardsAmount' => $cardsInstallments[0]->installments[0]->amount,

	            'tefs' => $tefs,
	            'tefsAmount' => $TEFsInstallments[0]->installments[0]->amount,

	            'bankSlips' => $bankSlips,
	            'bankSlipsAmount' => $bankSlipsInstallments[0]->installments[0]->amount,

	            'mesesVencimento' => $this->getMonths(),
	            'anosVencimento' => $this->getYears(),

				'campo_cpf' => Configuration::get(self::prefix . 'CAMPO_CPF'),
				'campo_fone' => Configuration::get(self::prefix . 'CAMPO_FONE'),

				'action_post' => $this->context->link->getModuleLink('bcash', 'validation')
	        )
	    );

    	$this->setTemplate('payment.tpl');
  	}

	/**
     * Set default medias for this controller
     */
    public function setMedia() {
        parent::setMedia();
        // Add you CSS and JS, 
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'bcash/resources/css/bcash_payment.css', 'all');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'bcash/resources/js/bcash_payment.js');
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

	private function getInstallments () {
		$email = Configuration::get(self::prefix . 'EMAIL');
		$token =  Configuration::get(self::prefix . 'TOKEN');

		$amount = $this->context->cart->getOrderTotal(true, Cart::BOTH);

		$installments = new Installments($email, $token);
		$installments->enableSandBox(true);

		$response = null;

		try {
 			$response = $installments->calculate($amount);

		} catch (ValidationException $e) {
		    echo "ErroTeste: " . $e->getMessage() . "\n";
		    echo "<pre>";
		    var_dump($e->getErrors());die;
		    echo "</pre>";

		} catch (ConnectionException $e) {
		    echo "ErroTeste: " . $e->getMessage() . "\n";
		    echo "<pre>";
		    var_dump($e->getErrors());die;
		    echo "</pre>";
		}

		return $response;
	}

	private function getCardInstallments($installments)
	{
		foreach ($installments as $key => $value) {
			if($value->name == "card"){
				return $value->paymentMethods;
			}
		}
	}
	
	private function getTEFSInstallments($installments)
	{
		foreach ($installments as $key => $value) {
			if($value->name == "transferencia"){
				return $value->paymentMethods;
			}
		}
	}
	
	private function getBankSlipInstallments($installments)
	{
		foreach ($installments as $key => $value) {
			if($value->name == "boleto"){
				return $value->paymentMethods;
			}
		}
	}

}
