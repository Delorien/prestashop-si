<?php

include_once dirname(__FILE__).'/../../domain/History.php';
include_once dirname(__FILE__).'/../../domain/PaymentDiscount.php';
include_once dirname(__FILE__).'/../../helper/PaymentMethodHelper.php';
include_once dirname(__FILE__).'/../../helper/FormatHelper.php';
include dirname(__FILE__).'/../../helper/BcashStateHelper.php';
include dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';
include_once dirname(__FILE__).'/payment.php';

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

			$this->writeHistory($response, $transactionRequest);

			$order = new Order($this->module->currentOrder);

		    //	Redirect	on	order	confirmation	page
			Tools::redirect('index.php?controller=order-confirmation' .
			'&id_cart='		. $this->context->cart->id .
			'&id_module='	. $this->module->id .
			'&reference_order='	. $order->id  .
			'&key='			. $this->context->customer->secure_key .
			'&bcash_transaction_id='	. $response->transactionId .
			'&bcash_paymentLink='		. $response->paymentLink .
			'&payment_method='		. Tools::getValue('payment-method') );

		} catch (ValidationException $e) {
	    	$this->retentativa($e);
		} catch (ConnectionException $e) {
			$this->retentativa($e);
		}

	}

	private function writeHistory($response, $transactionRequest)
	{
		$id_pedido = (int) $this->module->currentOrder;
		$id_transacao = $response->transactionId;
		$id_status = (int)(Configuration::get('PS_OS_BCASH_IN_PROGRESS'));
		$status = urldecode($response->descriptionStatus);

		$paymentMethodHelper = new PaymentMethodHelper();
		$pagamento_meio = $paymentMethodHelper->getById(Tools::getValue('payment-method'));

		if (PaymentMethodHelper::isCard($pagamento_meio)) {
			$parcelas = Tools::getValue('card-installment');
		}else {
			$parcelas = 1;
		}

		$history = new History($id_pedido, $id_transacao, $id_status, $status, $pagamento_meio->title, $parcelas);

		$history->write();
	}

	private function createTransactionRequest()
	{
	    $transactionRequest = new Bcash\Domain\TransactionRequest();

	    $transactionRequest->setSellerMail(Configuration::get(self::prefix.'EMAIL'));
	    $transactionRequest->setOrderId($this->createOrder());
	    $transactionRequest->setBuyer($this->createBuyer());
		$shoppingCost = $this->context->cart->getTotalShippingCost();
		$shoppingCost = FormatHelper::monetize($shoppingCost);
	    $transactionRequest->setShipping($shoppingCost);
	    $transactionRequest->setDiscount($this->calculateDiscounts());
	    $transactionRequest->setUrlNotification($this->context->link->getModuleLink('bcash', 'notification', array(), true));
	    $transactionRequest->setProducts($this->createProducts());
	    $transactionRequest->setAcceptedContract("S");
	    $transactionRequest->setViewedContract("S");

		$paymentMethodHelper = new PaymentMethodHelper();
		$paymentMethod = $paymentMethodHelper->getById(Tools::getValue('payment-method'));
		$transactionRequest->setPaymentMethod($paymentMethod->id);
		if (PaymentMethodHelper::isCard($paymentMethod)) {
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

	private function createBuyer()
	{
		$buyer = $this->context->customer;

	    $customer = new Bcash\Domain\Customer();
	    $customer->setMail($buyer->email);
	    $customer->setName($buyer->firstname . ' ' . $buyer->lastname);
	    $customer->setCpf($this->getCPF());
		$customer->setPhone($this->getTel());
		$address = $this->createAddress();
	    $customer->setAddress($address);

	    return $customer;
	}

	private function getCPF() 
	{
		if ( Tools::getValue('bcash_cpf') ) {
			return Tools::getValue('bcash_cpf');
		}else {
			$tabela = _DB_PREFIX_ . Configuration::get(self::prefix.'TABLE_CPF');
			$coluna = Configuration::get(self::prefix.'CAMPO_CPF_SELECT');
			$where = Configuration::get(self::prefix.'WHERE_CPF');

			$sql = 'SELECT ' . $coluna . ' FROM ' . $tabela . 
					' WHERE ' . $where . ' = ' . $this->context->customer->id;

			$result = Db::getInstance()->getValue($sql);
			return $result;
		}
	}

	private function getTel() 
	{
		$deliveryAddress = new Address((int) $this->context->cart->id_address_delivery);

		$phone = $deliveryAddress->phone;
		if($phone) {
			return $phone;
		}

		$phone_mobile = $deliveryAddress->phone_mobile;
		if($phone_mobile) {
			return $phone_mobile;
		}
	}

	private function createAddress()
	{
		$deliveryAddress = new Address((int) $this->context->cart->id_address_delivery);
	    $address = new Bcash\Domain\Address();
	    $address->setAddress($deliveryAddress->address1);
		$address->setNumber($this->getNumber());
	    $address->setNeighborhood($deliveryAddress->address2);
	    $address->setCity($deliveryAddress->city);
	    $address->setZipCode($deliveryAddress->postcode);
		$state = new State((int) $deliveryAddress->id_state);
		$address->setState(BcashStateHelper::getStateAbbreviation( $state->name ));

	    return $address;
	}

	private function getNumber() 
	{
		$campoNumero = Configuration::get(self::prefix . 'CAMPO_NUMERO_ENDERECO');
		if ( !empty($campoNumero) && $campoNumero == 'specified' ) {
			$tabela = _DB_PREFIX_ . Configuration::get(self::prefix.'TABLE_NUMERO_ENDERECO');
			$coluna = Configuration::get(self::prefix.'CAMPO_NUMERO_ENDERECO_SELECT');
			$where = Configuration::get(self::prefix.'WHERE_NUMERO_ENDERECO');
			$sql = 'SELECT ' . $coluna . ' FROM ' . $tabela . 
					' WHERE ' . $where . ' = ' . (int) $this->context->cart->id_address_delivery;
			$result = Db::getInstance()->getValue($sql);
			return $result;
		}
	}

	private function createProducts()
	{
		$cartProducts = $this->context->cart->getProducts();
		$products = array();

		foreach ($cartProducts as $product) {
		    $bcashProduct = new Bcash\Domain\Product();
		    $bcashProduct->setCode($product["id_product"]);
			$bcashProduct->setDescription($product["name"]);
		    $bcashProduct->setAmount($product["cart_quantity"]);
			$productCost = FormatHelper::monetize($product["price_wt"]);
		    $bcashProduct->setValue($productCost);
			array_push($products, $bcashProduct);
		}

	    return $products;
	}

	private function calculateDiscounts()
	{
		$paymentDiscount = new PaymentDiscount();

		$paymentMethodHelper = new PaymentMethodHelper();
		$payment_method = $paymentMethodHelper->getById(Tools::getValue('payment-method'));
		$order = new Order($this->module->currentOrder);

		if (PaymentMethodHelper::isCard($payment_method) && (Tools::getValue('card-installment') == 1)) {
			$paymentDiscount->apply($order, 'DESCONTO_CREDITO', $this->context);
		} else if (PaymentMethodHelper::isTEF($payment_method)) {
			$paymentDiscount->apply($order, 'DESCONTO_TEF', $this->context);
		} else if (PaymentMethodHelper::isBankSlip($payment_method)){
			$paymentDiscount->apply($order, 'DESCONTO_BOLETO', $this->context);
		}

		$totalDiscouts = $paymentDiscount->getAmountOrderDiscounts($order);

		return $totalDiscouts;
	}

	private function createCreditCard()
	{
	    $creditCard = new Bcash\Domain\CreditCard();
	    $creditCard->setHolder(Tools::getValue('card-owner-name'));
	    $creditCard->setNumber(Tools::getValue('card-number'));
	    $creditCard->setSecurityCode(Tools::getValue('card-security-code'));
	    $creditCard->setMaturityMonth(Tools::getValue('validade_mes_cartao'));
	    $creditCard->setMaturityYear(Tools::getValue('validade_ano_cartao'));
	    return $creditCard;
	}


	private function retentativa($e)
	{
		//See ParentOrderController.php L-74
		$oldCart = new Cart($this->context->cart->id);

		$duplication = $oldCart->duplicate();
		self::$cookie->id_cart = $duplication['cart']->id;
		self::$cookie->write();

		$this->cancelOrder();

		$params = array(
			'retentativa' => true,
			'b_errors' => $e->getErrors()->list,
		);


		// $url = $this->context->link->getModuleLink('bcash', 'payment', $params, [], true);
		$url = $this->context->link->getModuleLink('bcash', 'payment', $params);

		Tools::redirectLink($url);
	}

	private function cancelOrder() {
		$order_id = (int) $this->module->currentOrder;
		//$order_state_id = (int)(Configuration::get('PS_OS_BCASH_CANCELLED'));
		$order_state_id = 6;

		$history = new OrderHistory();
		$history->id_order = $order_id;
		$history->id_order_state = $order_state_id;
		$history->changeIdOrderState($order_state_id, $order_id);
		$history->add(true);
	}
}
