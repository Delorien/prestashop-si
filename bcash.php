<?php

if (! defined('_PS_VERSION_')) {
    exit();
}

include_once dirname(__FILE__).'/helper/BcashStatusHelper.php';
include_once dirname(__FILE__).'/helper/PaymentMethodHelper.php';

class Bcash extends PaymentModule
{
	const prefix = 'BCASH_';

	public function __construct() 
	{
		$this->name = 'bcash';
	    $this->tab = 'payments_gateways';
	    $this->version = '1.0.0';
	    $this->author = 'Bcash Dev Team';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Bcash');
	    $this->description = $this->l('Solução completa em pagamentos online.');

	 	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	}

	public function install()
	{
		if (!parent::install() ||
			!$this->registerHook('payment') ||
			!$this->registerHook('paymentReturn')) {

			return false;
		}

		 if (!$this->generateBcashOrderStatus()) {
            return false;
        }

		Configuration::updateValue(self::prefix . 'TITULO', 'Bcash');

		return true;
	}

	public function uninstall()
	{
	  if (!$this->deleteBcashOrderStatus() ||
	  		!Configuration::deleteByName('PS_OS_BCASH_IN_PROGRESS') ||
			!Configuration::deleteByName('PS_OS_BCASH_APPROVED') ||
			!Configuration::deleteByName('PS_OS_BCASH_COMPLETED') ||
			!Configuration::deleteByName('PS_OS_BCASH_IN_DISPUTE') ||
			!Configuration::deleteByName('PS_OS_BCASH_REFUNDED') ||
			!Configuration::deleteByName('PS_OS_BCASH_CANCELLED') ||
			!Configuration::deleteByName('PS_OS_BCASH_CHARGEBACK') ||
			!parent::uninstall()) {
	    return false;
	  }
	  return true;
	}

	//Método para a area de configuração do módulo 
	public function getContent()
	{
	    $output = null;

	 	//Verifica se o formulário foi submetido e retornou ao método ou se só deve cria-lo e exibi-lo para ser preenchido
	    if (Tools::isSubmit('btn_submit'))
	    {

	    	//Recupera valor dos arrays POST ou GET
	    	$titulo = strval(Tools::getValue('titulo'));
	    	$email = strval(Tools::getValue('email'));
			$consumer_key = strval(Tools::getValue('consumer_key'));
			$token = strval(Tools::getValue('bcash_token'));
			$desconto_boleto = strval(Tools::getValue('desconto_boleto'));
			$desconto_tef = strval(Tools::getValue('desconto_tef'));
			$desconto_credito = strval(Tools::getValue('desconto_credito'));
			$campo_cpf = strval(Tools::getValue('campo_cpf'));
			$campo_fone = strval(Tools::getValue('campo_fone'));
			$sandbox = strval(Tools::getValue('sandbox'));

	    	if (!empty($titulo)) {
        	    Configuration::updateValue(self::prefix . 'TITULO', $titulo);
			}
			if (!empty($email)) {
        	    Configuration::updateValue(self::prefix . 'EMAIL', $email);
			}
			if (!empty($consumer_key)) {
        	    Configuration::updateValue(self::prefix . 'CONSUMER_KEY', $consumer_key);
			}
			if (!empty($token)) {
        	    Configuration::updateValue(self::prefix . 'TOKEN', $token);
			}

       	    Configuration::updateValue(self::prefix . 'DESCONTO_BOLETO', $desconto_boleto);
       	    Configuration::updateValue(self::prefix . 'DESCONTO_TEF', $desconto_tef);
       	    Configuration::updateValue(self::prefix . 'DESCONTO_CREDITO', $desconto_credito);
			Configuration::updateValue(self::prefix . 'CAMPO_FONE', $campo_fone);

			Configuration::updateValue(self::prefix . 'CAMPO_CPF', $campo_cpf);
			if ($campo_cpf == 'specified') {
				$campo_cpf_select = strval(Tools::getValue('campo_cpf_select'));
				$table_cpf = strval(Tools::getValue('tableAjax'));
				Configuration::updateValue(self::prefix . 'CAMPO_CPF_SELECT', $campo_cpf_select);
				Configuration::updateValue(self::prefix . 'TABLE_CPF', $table_cpf);
			}

			if (!empty($sandbox)) {
        	    Configuration::updateValue(self::prefix . 'SANDBOX', 1);
			} else {
				Configuration::updateValue(self::prefix . 'SANDBOX', 0);
			}

		    $output .= $this->displayConfirmation($this->l('Configurações salvas!'));
	    }

	    return $output.$this->displayForm();
	}

	public function displayForm()
	{

		$this->context->controller->addCSS($this->getPathUri() . 'resources/css/bcash.css', 'all');
		$this->context->controller->addJS($this->getPathUri() . 'resources/js/jquery.validate.min.js', 'all');
		$this->context->controller->addJS($this->getPathUri() . 'resources/js/admin-form-validator.js', 'all');
		$this->context->smarty->assign('action_post', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

		$link = new Link(); 
		_PS_BASE_URL_.__PS_BASE_URI__."testadmin/".$link->getAdminLink('AdminTest', true);

		$this->context->smarty->assign(
	        array(
	            'titulo' => Configuration::get(self::prefix . 'TITULO'),
				'email' => Configuration::get(self::prefix . 'EMAIL'),
				'consumer_key' => Configuration::get(self::prefix . 'CONSUMER_KEY'),
				'bcash_token' => Configuration::get(self::prefix . 'TOKEN'),
				'desconto_boleto' => Configuration::get(self::prefix.'DESCONTO_BOLETO'),
				'desconto_tef' => Configuration::get(self::prefix.'DESCONTO_TEF'),
				'desconto_credito' => Configuration::get(self::prefix.'DESCONTO_CREDITO'),
				'sandbox' => Configuration::get(self::prefix.'SANDBOX'),
				'campo_cpf' => Configuration::get(self::prefix.'CAMPO_CPF'),
				'table_cpf' => Configuration::get(self::prefix.'TABLE_CPF'),
				'campo_cpf_select' => Configuration::get(self::prefix.'CAMPO_CPF_SELECT'),
				'campo_fone' => Configuration::get(self::prefix.'CAMPO_FONE'),
				'campos_fone' => array_keys(get_object_vars(new Customer())),
				'ajax_dir' => _MODULE_DIR_ . 'bcash/ajax_tables.php'
	        )
	    );

		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');
  	}

	public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }

		$this->context->controller->addCSS($this->getPathUri() . 'resources/css/bcash_option.css', 'all');
		$this->context->smarty->assign(
			array(
      			'payment_action_url' => $this->context->link->getModuleLink('bcash', 'payment')
      		)
  		);

        return $this->display(__FILE__, 'views/templates/hook/payment_option.tpl');
    }

	public function hookPaymentReturn($params)
	{
		if (!$this->active) {
			return;
		}

		$paymentMethodHelper = new PaymentMethodHelper();
		$paymentMethod = $paymentMethodHelper->getById(Tools::getValue('payment_method'));

		$this->context->controller->addCSS($this->getPathUri() . 'resources/css/bcash_payment_return.css', 'all');
		$this->context->smarty->assign(
			array(
				'bcash_payment_method' => $paymentMethod,
      			'bcash_transaction_id' => Tools::getValue('bcash_transaction_id'),
      			'bcash_paymentLink' => Tools::getValue('bcash_paymentLink')
      		)
  		);

		return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
	}


	private function generateBcashOrderStatus() 
	{

		foreach (BcashStatusHelper::getCustomOrderStatusBcash() as $key => $statusBcash) {

			$order_state = new OrderState();
            $order_state->module_name = 'bcash';
            $order_state->send_email = $statusBcash['send_email'];
            $order_state->color = $statusBcash['color'];
            $order_state->hidden = $statusBcash['hidden'];
            $order_state->delivery = $statusBcash['delivery'];
            $order_state->logable = $statusBcash['logable'];
            $order_state->invoice = $statusBcash['invoice'];
			$order_state->unremovable = $statusBcash['unremovable'];
			$order_state->shipped = $statusBcash['shipped'];
			$order_state->paid = $statusBcash['paid'];

			foreach (Language::getLanguages() as $language) {
				$order_state->name[(int) $language['id_lang']] = $statusBcash['name'];
			}

	        if ($order_state->add()) {//save new order status

				copy(dirname(__FILE__).'/logo.gif', _PS_ROOT_DIR_ . '/img/os/' . $order_state->id.'.gif');

				//Guarda referencia (id) do status criado na tabela ps_order_state, para facilitar na hora de recuperar
				Configuration::updateValue('PS_OS_BCASH_' . $key, (int)$order_state->id);
	        }
		}

		return true;
	}//generateBcashOrderStatus

	private function deleteBcashOrderStatus()
	{
		foreach (BcashStatusHelper::getCustomOrderStatusBcash() as $key => $statusBcash) {

			$order_state = new OrderState(Configuration::get('PS_OS_BCASH_' . $key));
			if (!$order_state->delete())
				return false;
		}
		return true;
	}//deleteBcashOrderStatus
}

