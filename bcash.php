<?php   

if (! defined('_PS_VERSION_')) {
    exit();
}

class bcash extends PaymentModule
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

		Configuration::updateValue(self::prefix . 'TITULO', 'Bcash'); 

		return true;
	}

	public function uninstall()
	{
	  if (!parent::uninstall()) {
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

		$this->context->smarty->assign(
	        array(
	            'titulo' => Configuration::get(self::prefix . 'TITULO'),
				'email' => Configuration::get(self::prefix . 'EMAIL'),
				'consumer_key' => Configuration::get(self::prefix . 'CONSUMER_KEY'),
				'bcash_token' => Configuration::get(self::prefix . 'TOKEN'),
				'desconto_boleto' => Configuration::get(self::prefix.'DESCONTO_BOLETO'),
				'desconto_tef' => Configuration::get(self::prefix.'DESCONTO_TEF'),
				'desconto_credito' => Configuration::get(self::prefix.'DESCONTO_CREDITO'),
				'sandbox' => Configuration::get(self::prefix.'SANDBOX')
	        )
	    );

		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');	   
  	}

	public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }

		$this->context->controller->addCSS($this->getPathUri() . 'resources/css/bcash_payment.css', 'all');
		$this->context->smarty->assign(
			array(
      			'my_module_name' => Configuration::get('MYMODULE_NAME'),
      			'module_dir' => _PS_MODULE_DIR_.$this->name.'/'
      		)
  		);

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

}
