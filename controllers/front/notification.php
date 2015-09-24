<?php

include_once dirname(__FILE__).'/../../bcash-php-sdk/autoloader.php';
include_once dirname(__FILE__).'/../../domain/History.php';

use Bcash\Service\Notification;
use Bcash\Service\Consultation;
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
				$this->writeHistory($orderId, $transactionId);
			}
		} catch (ValidationException $e) {
			$this->logUpdateFail($orderId, $transactionId, $statusId, $e);
		} catch (ConnectionException $e) {
			$this->logUpdateFail($orderId, $transactionId, $statusId, $e);
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

	private function writeHistory($orderId, $transactionId)
	{
		$email = Configuration::get(self::prefix . 'EMAIL');
		$token =  Configuration::get(self::prefix . 'TOKEN');

		$consultation = new Consultation($email, $token);
		$consultation->enableSandBox(true);

		try {
		    $response = $consultation->searchByTransaction($transactionId);

			$id_pedido = $response->transacao->id_pedido;
			$id_transacao = $transactionId;
			$id_status = $this->getStatus($response->transacao->cod_status);
			$status = $response->transacao->status;
			$pagamento_meio = $response->transacao->meio_pagamento;
			$parcelas = $response->transacao->parcelas;
			$valor_original = $response->transacao->valor_original;
			$valor_loja = $response->transacao->valor_loja;
			$taxa = $valor_original - $valor_loja;

			$history = new History($id_pedido, $id_transacao, $id_status, $status, $pagamento_meio, $parcelas, $valor_original, $valor_loja, $taxa);
			$history->write();

		} catch (ValidationException $e) {
			$this->logHistoryFail($orderId, $transactionId, $statusId, $e);
		} catch (ConnectionException $e) {
			$this->logHistoryFail($orderId, $transactionId, $statusId, $e);
		}
	}

	private function logUpdateFail ($orderId, $transactionId, $statusId, $e) 
	{
		$message = "Erro ao atualizar Transação: " . $transactionId . ', Pedido: ' . $orderId . ', Status: ' . $this->getStatus($statusId);
		if (! empty($e->getErrors()->erro->descricao)) {
			$message .= '. Menssagem do Erro: ' . $e->getErrors()->erro->descricao;
		}
		PrestaShopLogger::addLog($message, 3);
	}

	private function logHistoryFail ($orderId, $transactionId, $statusId, $e) 
	{
		$message = "Erro ao registrar histórico da atualizão da Transação: " . $transactionId . ', Pedido: ' . $orderId . ', Status: ' . $this->getStatus($statusId);
		if (! empty($e->getErrors()->erro->descricao)) {
			$message .= '. Menssagem do Erro: ' . $e->getErrors()->erro->descricao;
		}
		PrestaShopLogger::addLog($message, 3);
	}

}
