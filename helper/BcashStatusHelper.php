<?php

class BcashStatusHelper
{
	private static $order_status = array(
        'IN_PROGRESS' => 'Em andamento',
        'APPROVED' => 'Aprovado',
		'COMPLETED' => 'Completada',
    	'IN_DISPUTE' => 'Em disputa',
    	'REFUNDED' => 'Devolvida',
    	'CANCELLED' => 'Cancelada',
    	'CHARGEBACK' => 'Chargeback'
    );

 	private static $order_status_bcash = array(
        'IN_PROGRESS' => array(
            'name' => 'Em andamento',
            'color' => '#00FF99',
            'send_email' => false,
            'template' => '',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'APPROVED' => array(
            'name' => 'Aprovada',
            'color' => '#00FF99',
            'send_email' => false,
            'template' => '',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'COMPLETED' => array(
            'name' => 'Completada',
            'color' => '#00FF99',
            'send_email' => false,
            'template' => '',
            'hidden' => false,
            'delivery' => false,
            'logable' => true,
            'invoice' => true,
            'unremovable' => false,
            'shipped' => false,
            'paid' => true
        ),
        'IN_DISPUTE' => array(
            'name' => 'Em disputa',
            'color' => '#FF9999',
            'send_email' => false,
            'template' => '',
            'hidden' => true,
            'delivery' => false,
            'logable' => true,
            'invoice' => true,
            'unremovable' => false,
            'shipped' => false,
            'paid' => true
        ),
        'REFUNDED' => array(
            'name' => 'Devolvida',
            'color' => '#FFCC99',
            'send_email' => false,
            'template' => '',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'CANCELLED' => array(
            'name' => 'Cancelada',
            'color' => '#FF9999',
            'send_email' => false,
            'template' => '',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
		'CHARGEBACK' => array(
            'name' => 'Chargeback',
            'color' => '#FFCC99',
            'send_email' => false,
            'template' => '',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        )
    );

    public static function getOrderStatus()
    {
        return self::$order_status;
    }

	public static function getCustomOrderStatusBcash()
    {
        return self::$order_status_bcash;
    }

}
