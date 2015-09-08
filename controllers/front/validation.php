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
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{

		echo("<pre>");
		// print_r( $this->context->shop );die;
		// print_r( $this->context->cart );die;

		// $order = new Order((int)0);
// print_r( get_class_methods($this->module));die;
// print_r( Order::getOrderByCartId($this->context->cart->id) );die;
var_dump($this->createOrder());die;
		//$this->createBuyer( $this->context->customer ); die;
		//$this->createProduct( $this->context->cart->getProducts() ); die;
	}

	function createTransactionRequest()
	{
	    $transactionRequest = new Bcash\Domain\TransactionRequest();

	    $transactionRequest->setSellerMail(Configuration::get(self::prefix.'CAMPO_EMAIL'));
	    $transactionRequest->setOrderId("123456");
	    $transactionRequest->setBuyer(createBuyer());
	    $transactionRequest->setShipping(10.95);
	    $transactionRequest->setShippingType(ShippingTypeEnum::E_SEDEX);
	    $transactionRequest->setDiscount(1.20);
	    $transactionRequest->setAddition(3);
	    $transactionRequest->setPaymentMethod(PaymentMethodEnum::BANK_SLIP);
	    $transactionRequest->setUrlReturn("https://www.bcash.com.br/loja/retorno.php");
	    $transactionRequest->setUrlNotification("https://www.bcash.com.br/loja/aviso.php");
	    $transactionRequest->setProducts(createProduct());
	    $transactionRequest->setAcceptedContract("S");
	    $transactionRequest->setViewedContract("S");

	    return $transactionRequest;
	}

	private function createOrder()
    {
        $this->module->validateOrder(
            (int) $this->context->cart->id,
            Configuration::get('PS_OS_BCASH'),
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

	function createBuyer($buyer)
	{
	    $customer = new Bcash\Domain\Customer();
	    $customer->setMail($buyer->email);
	    $customer->setName($buyer->firstname . ' ' . $buyer->lastname);
	    $customer->setCpf(Tools::getValue('bcash_cpf'));
		$customer->setPhone(Tools::getValue('bcash_telefone'));
		$address = $this->createAddress( new Address((int) $this->context->cart->id_address_delivery) );
	    $customer->setAddress($address);

	    return $customer;
	}

	private function createAddress($deliveryAddress)
	{
	    $address = new Bcash\Domain\Address();
	    $address->setAddress($deliveryAddress->address1);
	    $address->setNeighborhood($deliveryAddress->address2);
	    $address->setCity($deliveryAddress->city);
	    $address->setZipCode($deliveryAddress->postcode);

		$state = new State((int) $deliveryAddress->id_state);
		$address->setState(BcashStateHelper::getStateAbbreviation( $state->name ));

	    return $address;
	}

	function createProduct($cartProducts)
	{
		$products = array();

		foreach ($cartProducts as $product) {
		    $bcashProduct = new Bcash\Domain\Product();
		    $bcashProduct->setCode($product["id_product"]);
			$bcashProduct->setDescription($product["name"]);
		    $bcashProduct->setExtraDescription($product["description_short"]);
		    $bcashProduct->setAmount($product["cart_quantity"]);
		    $bcashProduct->setValue($product["price"]);
			array_push($products, $bcashProduct);
		}

	    return $products;
	}

}













