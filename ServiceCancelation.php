<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
include_once(dirname(__FILE__).'/bcash-php-sdk/autoloader.php');
include_once dirname(__FILE__).'/domain/History.php';

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
$cancellation->enableSandBox(Configuration::get($prefix . 'SANDBOX'));

try {
    $response = $cancellation->execute($id_transacao);

	if($response->transactionStatusId == 7 || $response->transactionStatusId == 6) {
		updateOrder($id_pedido, $response);
		writeHistory($id_pedido, $response);
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
	$erros = $e->getErrors();

	if(!empty($erros)) {
		return ($e->getErrors()->list[0]->description);
	}
}

function updateOrder($orderId, $response) {

	$order_state_id = 6;
	/*$order_state_id = (int)(Configuration::get('PS_OS_BCASH_CANCELLED'));

	if ($response->transactionStatusId == 6) {
		$order_state_id = (int)(Configuration::get('PS_OS_BCASH_REFUNDED'));
	}*/

	$history = new OrderHistory();
	$history->id_order = $orderId;
	$history->id_order_state = $order_state_id;
	$history->changeIdOrderState($order_state_id, $orderId);
	$history->add(true);
}

function writeHistory($orderId, $response)
{
	$novoStatus = array();

	if ($response->transactionStatusId == 7) {
		$novoStatus['id'] = (int)(Configuration::get('PS_OS_BCASH_CANCELLED'));
		$novoStatus['status'] = 'Cancelada';
	} else {
		$novoStatus['id'] = (int)(Configuration::get('PS_OS_BCASH_REFUNDED'));
		$novoStatus['status'] = 'Devolvida';
	}

	$result = History::writeNewOrderStatus($orderId, $novoStatus);
}
