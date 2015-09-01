<?php

// require_once ( $_SERVER['DOCUMENT_ROOT'] . _MODULE_DIR_ . 'bcash/helper/PaymentMethodHelper.php');
include dirname(__FILE__).'/../../helper/PaymentMethodHelper.php';

class BcashPaymentModuleFrontController extends ModuleFrontController
{

	public $display_column_left = false;

	public function initContent()
  	{
	   	parent::initContent();

		$paymentMethodHelper = new PaymentMethodHelper();
		$cards = $paymentMethodHelper->getPaymentMethods()['CARD'];
		$tefs = $paymentMethodHelper->getPaymentMethods()['ONLINE_TRANSFER'];
		$bankSlips = $paymentMethodHelper->getPaymentMethods()['BANKSLIP'];

		$this->context->smarty->assign(
	        array(
	            'cards' => $cards,
	            'tefs' => $tefs,
	            'bankSlips' => $bankSlips,
	            'mesesVencimento' => $this->getMonths(),
	            'anosVencimento' => $this->getYears()
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

}
