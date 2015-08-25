<?php   

if (! defined('_PS_VERSION_')) {
    exit();
}

class bcash extends PaymentModule
{
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
		if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn')) { 
			return false;
		}
		 
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
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	    	//Recupera valor dos arrays POST ou GET
	        $my_module_name = strval(Tools::getValue('bcash'));
	        
	        if (!$my_module_name || empty($my_module_name) || !Validate::isGenericName($my_module_name)) {
	            	
	            $output .= $this->displayError($this->l('Invalid Configuration value'));
				
	        } else{
	        	
	            Configuration::updateValue('bcash', $my_module_name);
	            $output .= $this->displayConfirmation($this->l('Configurações salvas!'));
	        }
	    }
	    return $output.$this->displayForm();
	}
	
	public function displayForm()
	{
			
		$this->context->controller->addCSS($this->getPathUri() . 'resources/css/bcash.css', 'all');
		$this->context->controller->addJS($this->getPathUri() . 'resources/js/jquery.validate.min.js', 'all');
		$this->context->controller->addJS($this->getPathUri() . 'resources/js/admin-form-validator.js', 'all');
		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');	   
  	}
	
	
}
