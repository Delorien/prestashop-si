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

if (empty($id_transacao)) {
	echo(http_response_code(404));
	exit;
}

$email = Configuration::get($prefix . 'EMAIL');
$token =  Configuration::get($prefix . 'TOKEN');

$cancellation = new Cancellation($email, $token);
$cancellation->enableSandBox(true);

try {
    $response = $cancellation->execute($id_transacao);
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

