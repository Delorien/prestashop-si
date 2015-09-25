<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
include_once(dirname(__FILE__).'/bcash-php-sdk/autoloader.php');

use Bcash\Service\Cancellation;
use Bcash\Exception\ValidationException;
use Bcash\Exception\ConnectionException;

$error = 'error';
$prefix = 'BCASH_';

$id_transacao = Tools::getValue('id_transacao');
$id_pedido = Tools::getValue('id_pedido');

if (empty($id_transacao) || empty($id_pedido)) {
	echo(http_response_code(404));
	exit;
}

$email = Configuration::get($prefix . 'EMAIL');
$token =  Configuration::get($prefix . 'TOKEN');

$cancellation = new Cancellation($email, $token);
$cancellation->enableSandBox(true);

try {
    $response = $cancellation->execute($id_transacao);

	if($response->transactionStatusId == 7) {
		updateOrder($id_pedido);
	}

} catch (ValidationException $e) {
	die(errorResponse($e));
} catch (ConnectionException $e) {
	die(errorResponse($e));
}

echo(json_encode($response));
exit;

function errorResponse($e)
{
	if(!empty($e->getErrors())) {
		return ($e->getErrors()->list[0]->description);
	}
}

function updateOrder($orderId) {

	$order_state_id = (int)(Configuration::get('PS_OS_BCASH_CANCELLED'));;

	$history = new OrderHistory();
	$history->id_order = $orderId;
	$history->id_order_state = $order_state_id;
	$history->changeIdOrderState($order_state_id, $orderId);
	$history->add(true);
}
