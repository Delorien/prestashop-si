<?php

class PaymentMethodHelper
{

	const CARD_TYPE = "CARD";
	const BANKSLIP_TYPE = "BANKSLIP";
	const ONLINE_TRANSFER_TYPE = "ONLINE_TRANSFER";

	private static $cards;
	private static $onlineTransfer;
	private static $bankSlip;

	public function __construct() {
		self::$cards = array(
			$this->createPayment(1, 'Visa', self::CARD_TYPE, 1.0, 12),
			$this->createPayment(2, 'Mastercard', self::CARD_TYPE, 1.0, 12),
			$this->createPayment(37, 'American Express', self::CARD_TYPE, 1.0, 12),
			$this->createPayment(45, 'Aura', self::CARD_TYPE, 1.0, 24),
			$this->createPayment(55, 'Diners', self::CARD_TYPE, 1.0, 12),
			$this->createPayment(56, 'HiperCard', self::CARD_TYPE, 1.0, 12),
			$this->createPayment(63, 'Elo', self::CARD_TYPE, 1.0, 12)
		);

		self::$bankSlip = array(
			$this->createPayment(10, 'Boleto Bancário', self::BANKSLIP_TYPE, 0.01, 1)
		);

		self::$onlineTransfer = array(
			$this->createPayment(58, 'Banco do Brasil', self::ONLINE_TRANSFER_TYPE, 0.01, 1),
			$this->createPayment(59, 'Banco Bradesco', self::ONLINE_TRANSFER_TYPE, 0.01, 1),
			$this->createPayment(60, 'Banco Itaú', self::ONLINE_TRANSFER_TYPE, 0.01, 1),
			$this->createPayment(61, 'Banco Banrisul', self::ONLINE_TRANSFER_TYPE, 0.01, 1),
			$this->createPayment(62, 'Banco HSBC', self::ONLINE_TRANSFER_TYPE, 0.01, 1)
		);
	}

	private function createPayment($id, $title, $type, $minimunValue, $maxInstallments) {
		$payment = new stdClass();

		$payment->id = $id;
		$payment->title = $title;
		$payment->type = $type;
		$payment->minimunValue = $minimunValue;
		$payment->maxInstallments = $maxInstallments; 

		return $payment;
	}

	public function getById($id) {

		foreach (self::$cards as $method) {
			if($method->id == $id){
				return $method;
			}
		}

		foreach (self::$bankSlip as $method) {
			if($method->id == $id){
				return $method;
			}
		}

		foreach (self::$onlineTransfer as $method) {
			if($method->id == $id){
				return $method;
			}
		}
	}

	public function getPaymentMethods() {
		return array(
			self::CARD_TYPE => self::$cards,
			self::BANKSLIP_TYPE => self::$bankSlip,
			self::ONLINE_TRANSFER_TYPE => self::$onlineTransfer
		);
	}

	static public function isCard($paymentMethod) 
	{
		if($paymentMethod->type == self::CARD_TYPE){
			return true;
		}
		return false;
	}

	static public function isTEF($paymentMethod) 
	{
		if($paymentMethod->type == self::ONLINE_TRANSFER_TYPE){
			return true;
		}
		return false;
	}

	static public function isBankSlip($paymentMethod) 
	{
		if($paymentMethod->type == self::BANKSLIP_TYPE){
			return true;
		}
		return false;
	}

}
