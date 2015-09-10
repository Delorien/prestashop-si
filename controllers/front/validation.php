<?php

include dirname(__FILE__).'/../../helper/BcashStateHelper.php';
include dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';

use Bcash\Domain\StateEnum;
use Bcash\Domain\PaymentMethod;
use Bcash\Domain\PaymentMethodEnum;
use Bcash\Domain\Product;
use Bcash\Domain\TransactionRequest;
use Bcash\Domain\ShippingTypeEnum;
use Bcash\Domain\CurrencyEnum;
use Bcash\Service\Payment;
use Bcash\Exception\ConnectionException;
use Bcash\Exception\ValidationException;


class BcashValidationModuleFrontController extends ModuleFrontController
{
	const prefix = 'BCASH_';

	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$payment = new Payment(Configuration::get(self::prefix.'CONSUMER_KEY'));
		$payment->enableSandBox(Configuration::get(self::prefix.'SANDBOX'));

		$transactionRequest = $this->createTransactionRequest();

		try {
		    $response = $payment->create($transactionRequest);
		    echo "<pre>";
    		print_r($response);die;
		    echo "</pre>";
		} catch (ValidationException $e) {
		    echo "ErroTeste: " . $e->getMessage() . "\n";
		    echo "<pre>";
		    print_r($e->getErrors());die;
		    echo "</pre>";
		} catch (ConnectionException $e) {
		    echo "ErroTeste: " . $e->getMessage() . "\n";
		    echo "<pre>";
		    print_r($e->getErrors());die;
		    echo "</pre>";
		}

	}

	function createTransactionRequest()
	{
	    $transactionRequest = new Bcash\Domain\TransactionRequest();

	    $transactionRequest->setSellerMail(Configuration::get(self::prefix.'EMAIL'));
	    $transactionRequest->setOrderId($this->createOrder());
	    $transactionRequest->setBuyer($this->createBuyer());
		$shoppingCost = $this->context->cart->getTotalShippingCost();
		$shoppingCost = number_format(Tools::ps_round($shoppingCost, 2), 2, '.', '');
	    $transactionRequest->setShipping($shoppingCost);
	    $transactionRequest->setDiscount($this->getCartDiscounts());
	    $transactionRequest->setUrlReturn("https://www.bcash.com.br/loja/retorno.php");
	    $transactionRequest->setUrlNotification("https://www.bcash.com.br/loja/aviso.php");
	    $transactionRequest->setProducts($this->createProducts());
	    $transactionRequest->setAcceptedContract("S");
	    $transactionRequest->setViewedContract("S");

		$paymentMethod = Tools::getValue('payment-method');
		$transactionRequest->setPaymentMethod($paymentMethod);
		if ($this->isCard($paymentMethod)) {
			$transactionRequest->setInstallments(Tools::getValue('card-installment'));
			$transactionRequest->setCreditCard($this->createCreditCard());
		}

	    return $transactionRequest;
	}

	private function createOrder()
    {
        $this->module->validateOrder(
            (int) $this->context->cart->id,
            Configuration::get('PS_OS_BCASH_IN_PROGRESS'),
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            $this->module->displayName,
            null,
            null,
            (int) $this->context->currency->id,
            false,
            $this->context->customer->secure_key
        );

        return $this->module->currentOrder;
    }

	function createBuyer()
	{
		$buyer = $this->context->customer;

	    $customer = new Bcash\Domain\Customer();
	    $customer->setMail($buyer->email);
	    $customer->setName($buyer->firstname . ' ' . $buyer->lastname);
	    $customer->setCpf(Tools::getValue('bcash_cpf'));
		$customer->setPhone(Tools::getValue('bcash_telefone'));
		$address = $this->createAddress();
	    $customer->setAddress($address);

	    return $customer;
	}

	private function createAddress()
	{
		$deliveryAddress = new Address((int) $this->context->cart->id_address_delivery);
	    $address = new Bcash\Domain\Address();
	    $address->setAddress($deliveryAddress->address1);
	    $address->setNeighborhood($deliveryAddress->address2);
	    $address->setCity($deliveryAddress->city);
	    $address->setZipCode($deliveryAddress->postcode);

		$state = new State((int) $deliveryAddress->id_state);
		$address->setState(BcashStateHelper::getStateAbbreviation( $state->name ));

	    return $address;
	}

	function createProducts()
	{
		$cartProducts = $this->context->cart->getProducts();
		$products = array();

		foreach ($cartProducts as $product) {
		    $bcashProduct = new Bcash\Domain\Product();
		    $bcashProduct->setCode($product["id_product"]);
			$bcashProduct->setDescription($product["name"]);
		    $bcashProduct->setAmount($product["cart_quantity"]);
			$productCost = number_format(Tools::ps_round($product["price_wt"], 2), 2, '.', '');
		    $bcashProduct->setValue($productCost);
			array_push($products, $bcashProduct);
		}

	    return $products;
	}

	private function getCartDiscounts()
    {
        $cart_discounts = $this->context->cart->getDiscounts();

        $totalDiscouts = (float) 0;

        if (count($cart_discounts) > 0) {
            foreach ($cart_discounts as $discount) {
                $totalDiscouts += $discount['value_real'];
            }
        }

        return number_format(Tools::ps_round($totalDiscouts, 2), 2, '.', '');
    }

	private function isCard($paymentMethod) 
	{
		if( in_array($paymentMethod, array(1, 2, 37, 45, 55, 56, 63)) ){
			return true;
		}
		return false;
	}

	function createCreditCard()
	{
	    $creditCard = new Bcash\Domain\CreditCard();
	    $creditCard->setHolder(Tools::getValue('card-owner-name'));
	    $creditCard->setNumber(Tools::getValue('card-number'));
	    $creditCard->setSecurityCode(Tools::getValue('card-security-code'));
	    $creditCard->setMaturityMonth(Tools::getValue('validade_mes_cartao'));
	    $creditCard->setMaturityYear(Tools::getValue('validade_ano_cartao'));
	    return $creditCard;
	}

}













