<?php

class PaymentDiscount
{

	const billion =  1000000000;
	const prefix  = 'PS_ID_';
	const bcash  = 'BCASH_';

	private $names = array(
			'DESCONTO_CREDITO',
			'DESCONTO_TEF',
			'DESCONTO_BOLETO'
		);

	public function createDefaultBcashDiscounts()
	{
		foreach ($this->names as $name) {
			$coupon = new Discount();

			$coupon->name = $this->getLangsForName($name);
			$coupon->quantity = self::billion;
   			$coupon->quantity_per_user = self::billion;
   			$coupon->date_from = date('Y-m-d H:i:s');
			$coupon->date_to = date('Y-m-d',strtotime('+30 year'));
			$coupon->partial_use = 0;
			$coupon->code = 'gerenciado_pelo_modulo_' . Tools::passwdGen(8);
			$coupon->active = 0;
			//Invisivel
			$coupon->highlight = 0;
			//Envio excluido
			$coupon->minimum_amount_shipping = 0;

			//Acoes
			$coupon->free_shipping = 0;
			$coupon->reduction_percent = 0;

			if ($coupon->add()) {
				Configuration::updateValue(self::prefix . self::bcash . $name, (int)$coupon->id);
	        }
		}

		return true;
	}

	public function deleteDefaultBcashDiscounts()
	{
		foreach ($this->names as $name) {
			$couponId = Configuration::get(self::prefix . self::bcash . $name);
			$coupon = new CartRule($couponId);
			$coupon->delete();
		}
		return true;
	}

	private function getLangsForName($cart_rule_name) 
	{
		$languages = Language::getLanguages();
		foreach ($languages as $key => $language) {
			$array[$language['id_lang']]= $cart_rule_name;
		}

		return $array;
	}

	static public function update($paymentType, $value)
	{
		Configuration::updateValue(self::bcash . $paymentType, $value);

		$couponId = Configuration::get(self::prefix . self::bcash . $paymentType);
		PaymentDiscount::resetActions($couponId);
		 if (!empty($value)) {
			PaymentDiscount::updateReductionPercent($couponId, $value);
		 	PaymentDiscount::enable($couponId);
		 } else {
		 	PaymentDiscount::updateReductionPercent($couponId, 0);
		 	PaymentDiscount::disable($couponId);
		 }
	}

	static private function resetActions($id)
	{
		$coupon = new CartRule($id);
		$coupon->reduction_amount = 0;
		$coupon->product_restriction = 0;
		$coupon->reduction_product = 0;
		$coupon->free_shipping = 0;
		$coupon->minimum_amount_shipping = 0;
		$coupon->partial_use = 0;
		$coupon->highlight = 0;
		$coupon->update();
	}

	static public function enable($id)
	{
		$coupon = new CartRule($id);
		$coupon->active = 1;
		$coupon->update();
	}

	static public function disable($id)
	{
		$coupon = new CartRule($id);
		$coupon->active = 0;
		$coupon->update();
	}

	static public function updateReductionPercent($id, $newPercent)
	{
		$coupon = new CartRule($id);
		$coupon->reduction_percent = $newPercent;
		$coupon->update();
	}

}
