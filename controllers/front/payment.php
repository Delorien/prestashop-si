<?php

class BcashPaymentModuleFrontController extends ModuleFrontController
{

	public $display_column_left = false;

	public function initContent()
  	{
  		$this->display_column_left = false;
    	parent::initContent();
    	$this->setTemplate('payment.tpl');
  	}
}
