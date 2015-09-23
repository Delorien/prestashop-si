<?php

include_once dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';

use Bcash\Service\Notification;
use Bcash\Domain\NotificationContent;
use Bcash\Domain\NotificationStatusEnum;
use Bcash\Exception\ValidationException;
use Bcash\Exception\ConnectionException;

class BcashNotificationModuleFrontController extends ModuleFrontController 
{
	const prefix = 'BCASH_';

	 /*
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		/* Dados do post enviado pelo Bcash */
		$transactionId = Tools::getValue('transacao_id');
		$orderId = Tools::getValue('pedido');
		$statusId = Tools::getValue('status_id');

		$email = Configuration::get(self::prefix . 'EMAIL');
		$token =  Configuration::get(self::prefix . 'TOKEN');

		$notificationContent = new NotificationContent($transactionId, $orderId, $statusId);
		$notification = new Notification($email, $token, $notificationContent);
		$notification->enableSandBox(true);

		$transactionValue = $this->getOrderValue($orderId);

		try {
		    $result = $notification->verify($transactionValue);

			if ($result) {
				$this->updateStatus($orderId, $statusId);
			}
		} catch (ValidationException $e) {
			// PrestaShopLogger::addLog("ErroTeste: " . $e->getErrors()->limt[0], 1);
		} catch (ConnectionException $e) {
			// PrestaShopLogger::addLog("ErroTeste: " . $e->getErrors()->limt[0], 1);
		}
	}

	private function updateStatus($orderId, $statusId)
	{
		$order_state_id = $this->getStatus($statusId);

		$history = new OrderHistory();
		$history->id_order = $orderId;
		$history->id_order_state = $order_state_id;
		$history->changeIdOrderState($order_state_id, $orderId);
		$history->add(true);
	}

	private function getStatus($statusId) 
	{
		$order_status = array(
	         1 => 'IN_PROGRESS',
	         3 => 'APPROVED',
			 4 => 'COMPLETED',
	    	 5 => 'IN_DISPUTE',
	    	 6 => 'REFUNDED',
	    	 7 => 'CANCELLED',
	    	 8 => 'CHARGEBACK'
	    );

		return (int)(Configuration::get('PS_OS_BCASH_' . $order_status[$statusId]));
	}

	private function getOrderValue($orderId)
	{
		$order = new Order($orderId);
		$total_price = number_format(Tools::ps_round($order->total_paid, 2), 2, '.', '');
		return $total_price;
	}

}
