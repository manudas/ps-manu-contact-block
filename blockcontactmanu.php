<?php

/*

* 2007-2015 PrestaShop

*

* NOTICE OF LICENSE

*

* This source file is subject to the Academic Free License (AFL 3.0)

* that is bundled with this package in the file LICENSE.txt.

* It is also available through the world-wide-web at this URL:

* http://opensource.org/licenses/afl-3.0.php

* If you did not receive a copy of the license and are unable to

* obtain it through the world-wide-web, please send an email

* to license@prestashop.com so we can send you a copy immediately.

*

* DISCLAIMER

*

* Do not edit or add to this file if you wish to upgrade PrestaShop to newer

* versions in the future. If you wish to customize PrestaShop for your

* needs please refer to http://www.prestashop.com for more information.

*

*  @author PrestaShop SA <contact@prestashop.com>

*  @copyright  2007-2015 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)

*  International Registered Trademark & Property of PrestaShop SA

*/

require_once(dirname(__FILE__)."/models/callmepleasedata_data.php");
require_once(dirname(__FILE__)."/models/callmepleasedata.php");
require_once(dirname(__FILE__)."/models/callmepleasecommentsdata.php");

if (!defined('_CAN_LOAD_FILES_'))

	exit;



class Blockcontactmanu extends Module

{

	public function __construct()

	{

		$this->name = 'blockcontactmanu';

		$this->author = 'Manu';

		$this->tab = 'front_office_features';

		$this->version = '1.4.0';



		$this->bootstrap = true;

		parent::__construct();



		$this->displayName = $this->l('Contact block - Bloque de contacto de Manu + CallmePlease');

		$this->description = $this->l('Allows you to add additional information about your store\'s customer service.');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

	}



	public function install()

	{
		//die("añadir hook para backoffice que muestre las llamadas entrantes+funcion de gestion del hook+plantilla del hook");
		//die("añadir bd callmeplease de telerosa aquí en la definici�n. podemos llamarla algo así como contactmanucallmeplase");
		//die("añadir a la tabla anterior los campos de ip, estado de atenci�n, estado de bloqueo (por cookie, por sesion, por ip), session id");
		//die("depende de la configuraci�n la session id la podemos coger de la cookie o de session o de ambos");
		
		$this->installAdminTabs();
		
		return parent::install()
		
			&& $this->installSQL()

			&& Configuration::updateValue('BLOCKCONTACT_TELNUMBER', '')
			
			&& Configuration::updateValue('BLOCKCONTACT_SHOWNTELNUMBER', '')

			&& Configuration::updateValue('BLOCKCONTACT_EMAIL', '')
			
			&& Configuration::updateValue('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED', '')
			
			&& Configuration::updateValue('CALLMEPLEASE_HOST', '')
			
			&& Configuration::updateValue('CALLMEPLEASE_ADMIN_FOLDER', '')
			
			&& Configuration::updateValue('CALLMEPLEASE_DOMAINS', '')
			
			&& $this->registerHook('displayNav')

			&& $this->registerHook('displayHeader')
			
			&& $this->registerHook('displayBackOfficeTop')
			
			&& $this->registerHook('displayBackOfficeHeader');

	}
	
	private function installAdminTabs(){
		$languageArray = Language::getLanguages(false);
			
		// Install Tabs
		// PARENT TAB
		$parent_tab = new Tab();
		// Need a foreach for the language
		foreach ($languageArray as $language)
			$parent_tab->name[$language['id_lang']] = $this->l('Solicitud llamadas entrantes');
		$parent_tab->class_name = 'Blockcontactmanu';
		$parent_tab->id_parent = 0; // Home tab
		$parent_tab->module = $this->name;
		$parent_tab->add();
			
		$gestion_llamadas_tab = new Tab();
		foreach ($languageArray as $language)
			$gestion_llamadas_tab->name[$language['id_lang']] = $this->l('Datos de solicitudes');
		$gestion_llamadas_tab->class_name = 'AdminCallmePlease';
		$gestion_llamadas_tab->id_parent = $parent_tab->id; // CallmePlease parent tab
		$gestion_llamadas_tab->module = $this->name;
		$gestion_llamadas_tab->add();
		
		$comments_llamadas_tab = new Tab();
		// Need a foreach for the language
		foreach ($languageArray as $language)
			$comments_llamadas_tab->name[$language['id_lang']] = $this->l('Comentarios de llamadas');
		$comments_llamadas_tab->class_name = 'AdminCallmePleaseComments';
		$comments_llamadas_tab->id_parent = $parent_tab->id; // CallmePlease parent tab
		$comments_llamadas_tab->module = $this->name;
		$comments_llamadas_tab->add();
		
	}
	
	private function uninstallAdminTabs(){
		$resultCollection = Tab::getCollectionFromModule($this->name);
			
		$deflatedResult = $resultCollection->getResults();	
			
		foreach ($deflatedResult as $tab){
			 $tab->delete();
		}
	}

	private function installSQL(){
		require_once (dirname(__FILE__)."/sql/install.php");
		$noerror = true;
		for($i = 0; $i < count($SQL); $i++){
			if ($noerror == false) return $noerror;
			$noerror = $noerror && Db::getInstance()->Execute($SQL[$i]);
		}
		return $noerror;
	}
	
	public function uninstall()
	{
		$this->uninstallAdminTabs();
		return $this->uninstallSQL()
				&& Configuration::deleteByName('CALLMEPLEASE_DOMAINS')
				&& Configuration::deleteByName('CALLMEPLEASE_HOST')
				&& Configuration::deleteByName('CALLMEPLEASE_ADMIN_FOLDER')
				&& Configuration::deleteByName('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED') 
				&& Configuration::deleteByName('BLOCKCONTACT_TELNUMBER') 
				&& Configuration::deleteByName('BLOCKCONTACT_SHOWNTELNUMBER') 
				&& Configuration::deleteByName('BLOCKCONTACT_EMAIL') 
				&& parent::uninstall();
	}

	public function uninstallSQL(){
		// return true;
		require_once (dirname(__FILE__)."/sql/uninstall.php");
		$noerror = true;
		for($i = 0; $i < count($SQL); $i++){
			if ($noerror == false) return $noerror;
			$noerror = $noerror && Db::getInstance()->Execute($SQL[$i]);
		}
		// return true;
		return $noerror;
	}


	public function getContent()

	{

		$html = '';

		// If we try to update the settings

		if (Tools::isSubmit('submitModule'))

		{
			
		
			$host = Tools::getValue('callmeplease_host');
			$admin_folder = Tools::getValue('callmeplease_admin_folder');
			
			if (!empty($host) && empty($admin_folder)  ){
				
				$this->errors[] = Tools::displayError('Si rellena el host debe hacerlo tambien con la carpeta de administracion remota del sistema');
				$html .= $this->displayError($this->errors);
			}
			else if (empty($admin_folder)){
				$this->errors[] = Tools::displayError('Siempre debe rellenar la carpeta de admnistración del sistema. La remota o la local');
				$html .= $this->displayError($this->errors);
			}
			else {
			
				// $callmeplease_activated = Tools::getValue('blockcontact_callmeplease_activated');
	
				Configuration::updateValue('BLOCKCONTACT_TELNUMBER', Tools::getValue('blockcontact_telnumber'));
				
				Configuration::updateValue('BLOCKCONTACT_SHOWNTELNUMBER', Tools::getValue('blockcontact_showntelnumber'));
	
				Configuration::updateValue('BLOCKCONTACT_EMAIL', Tools::getValue('blockcontact_email'));
	
				Configuration::updateValue('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED', Tools::getValue('blockcontact_callmeplease_activated'));
				
				Configuration::updateValue('CALLMEPLEASE_HOST', $host);
				
				Configuration::updateValue('CALLMEPLEASE_ADMIN_FOLDER', $admin_folder);
				
				Configuration::updateValue('CALLMEPLEASE_DOMAINS', Tools::getValue('callmeplease_domains'));
								
				// $this->_clearCache('blockcontact.tpl');
				Tools::clearCache(null, 'blockcontact.tpl');
				
				// $this->_clearCache('nav.tpl');
				Tools::clearCache(null, 'nav.tpl');
	
				$html .= $this->displayConfirmation($this->l('Configuration updated'));
			}
		}



		$html .= $this->renderForm();



		return $html;

	}



	public function hookDisplayHeader($params)

	{
		$this->context->controller->addCSS(($this->_path).'css/blockcontact.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/blockcontactmanu.js');

		$this->context->controller->addCSS((_THEME_CSS_DIR_).'noto-fonts.css', 'all');
		
		$this->context->controller->addJqueryUI('ui.dialog');
		$this->context->controller->addJqueryUI('ui.draggable');
		$this->context->controller->addJqueryUI('ui.resizable');
		
		$this->context->controller->addJqueryUI('ui.effect');
		$this->context->controller->addJqueryUI('ui.effect-explode');
		$this->context->controller->addJqueryUI('ui.effect-blind');
		
	}

	public function checkCallmepleaseActivation(){
		
		$_host = Configuration::get('CALLMEPLEASE_HOST');
		if (!empty($_host)){
			/*
			$_admin_folder = Configuration::get('CALLMEPLEASE_ADMIN_FOLDER');
			// $path = $this->context->link->getAdminLink('AdminCallmePlease', false);
			
			if (substr($_host, -1) == '/'){
				$_host = substr($_host, 0, -1);
			}
			if (empty($_host)) $_host .= '/';

			if (substr($_admin_folder, -1) == '/'){
				$_admin_folder = substr($_admin_folder, 0, -1);
			}
			// $path = $this->context->link->getModuleLink('blockcontactmanu','AdminCallmePleaseController');
			$path = $this->context->link->getAdminLink('AdminCallmePlease');
			$fullCallMePath = $_host.'/'.$_admin_folder.'/'.$path;
			*/
			$fullCallMePath = $this->getAjaxControllerPath();
			
			$options = "?action=checkCallmepleaseActivation&ajax=true";
			
			// we strip protocol or relative protocol
			$end_protocol_position = strpos ( $fullCallMePath , "//");
			if ($end_protocol_position !== false){ // Changing protocol to relative
				$fullCallMePath = substr($fullCallMePath, $end_protocol_position+mb_strlen("//"));
			}
				
			// var_dump($host_url);
			
			// create curl resource
			$ch = curl_init();
			
			// set url
			curl_setopt($ch, CURLOPT_URL, $fullCallMePath.$options);
			
			//return the transfer as a string
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			// $output contains the output string
			$output = curl_exec($ch);
			
			// close curl resource to free up system resources
			curl_close($ch);
			// return true;
			return ($output == '1');
			
		}
		else { 
			// die (var_export(Configuration::get('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED') == 'true', true)."holi");
			$status = Configuration::get('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED');
			return ($status == '1');
		}
	}

	public function hookDisplayRightColumn($params)

	{

		global $smarty;

		$tpl = 'blockcontact';

		if (isset($params['blockcontact_tpl']) && $params['blockcontact_tpl'])

			$tpl = $params['blockcontact_tpl'];

		//if (!$this->isCached($tpl.'.tpl', $this->getCacheId()))
			
			$fullCallMePath = $this->getAjaxControllerPath(); // SOLO CACHEA ESTE PARAMETRO
			
			$activation = $this->checkCallmepleaseActivation();
			
			// die($fullCallMePath);
			$smarty->assign(array(

				'telnumber' => Configuration::get('BLOCKCONTACT_TELNUMBER'),
				
				'showntelnumber' => Configuration::get('BLOCKCONTACT_SHOWNTELNUMBER'),
				
				'callmeplease_activated' => $activation,
				
				'callmeplease_path' => $fullCallMePath,

				'email' => Configuration::get('BLOCKCONTACT_EMAIL')

			));
		Tools::clearCache(null, $tpl.'.tpl');
		return $this->display(__FILE__, $tpl.'.tpl'/*, $this->getCacheId()*/);

	}
	
	public function getAjaxControllerPath(){
		$callMePleaseHost = Configuration::get('CALLMEPLEASE_HOST');
		if (empty($callMePleaseHost)) {
			$callMePleaseHost = _PS_BASE_URL_.__PS_BASE_URI__;
		}
		if (substr($callMePleaseHost, -1) == '/'){
			$callMePleaseHost = substr($callMePleaseHost, 0, -1);
		}
		if (!empty($callMePleaseHost)) $callMePleaseHost .= '/';
		
		$end_protocol_position = strpos ( $callMePleaseHost , "//");
		if ($end_protocol_position !== false){ // Changing protocol to relative
			$callMePleaseHost = substr($callMePleaseHost, $end_protocol_position);
		}
		else { // el protocolo no se ha especificado en el host destino, añadimos protocolo relativo
			$callMePleaseHost = '//'.$callMePleaseHost;
		}
		
		// if (!empty($callMePleaseHost))  $callMePleaseHost = "";
		// $path = $this->context->link->getModuleLink('blockcontactmanu','AdminCallmePleaseController');
		$params['module'] = 'blockcontactmanu';
		$params['controller'] = 'FrontCallmePlease';
		//$path = Context::getContext()->link->getModuleLink('blockcontactmanu','FrontCallmePlease');
		$rewriting = (int)Configuration::get('PS_REWRITING_SETTINGS');
		$fullCallMePath = $callMePleaseHost.Dispatcher::getInstance()->createUrl('module', /* $id_lang*/ null, $params, /*force routes */ $rewriting, '', /* $id_shop*/ null);
		// $fullCallMePath = $callMePleaseHost.$path;
		return $fullCallMePath;
	}

	
	public function hookDisplayLeftColumn($params)

	{

		return $this->hookDisplayRightColumn($params);

	}



	public function hookDisplayNav($params)

	{

		$params['blockcontact_tpl'] = 'nav';

		return $this->hookDisplayRightColumn($params);

	}



	public function renderForm()

	{

		$callme_options = array(
			array( 'blockcontact_callmeplease_activated' => 'callmeplease_on', 'value' => 1, 'label' => $this->l('Yes')),
			array( 'blockcontact_callmeplease_activated' => 'callmeplease_off', 'value' => 0, 'label' => $this->l('No'))
        );
		
		
		$fields_form = array(

			'form' => array(

				'legend' => array(

					'title' => $this->l('Settings'),

					'icon' => 'icon-cogs'

				),

				'description' => $this->l('This block displays in the header your phone number (‘Call us now’), and a link to the ‘Contact us’ page.').'<br/><br/>'.

						$this->l('To edit the email addresses for the ‘Contact us’ page: you should go to the ‘Contacts’ page under the ‘Customer’ menu.').'<br/>'.

						$this->l('To edit the contact details in the footer: you should go to the ‘Contact Information Block’ module.'),

				'input' => array(

					array(

						'type' => 'text',

						'label' => $this->l('Telephone number where the customer calls when clicking'),

						'name' => 'blockcontact_telnumber',

					),
					
					array(

						'type' => 'text',

						'label' => $this->l('Shown telephone number'),

						'name' => 'blockcontact_showntelnumber',

					),

					array(

						'type' => 'text',

						'label' => $this->l('Email'),

						'name' => 'blockcontact_email',

						'desc' => $this->l('Enter here your customer service contact details.'),

					),
					
					array(

						'type' => 'radio',

						'label' => $this->l('Activar peticion de llamadas entrantes'),

						'name' => 'blockcontact_callmeplease_activated',

						'desc' => $this->l('Seleccione si desea activar la recepci�n de peticion de llamadas'),
						
						'values' => $callme_options,

						'is_bool' => true,
					),
					array(

						'type' => 'text',

						'label' => $this->l('Host que centraliza la solicitud de llamadas'),

						'name' => 'callmeplease_host',

						'desc' => $this->l('Si centraliza la solicitud de llamadas entrantes en otra p�gina distinta a esta, introduzca su URL de la forma m�s relativa posible.'),

					),
					
					array(
						
							'type' => 'text',
						
							'label' => $this->l('Introduzca la carpeta de administracion del sistema.'),
						
							'name' => 'callmeplease_admin_folder',
						
							'desc' => $this->l('Campo obligatorio. Debe introducir el local o el remoto si este host no centraliza las solicitudes: Direccion de la carpeta de administracion de su sitio prestashop que centraliza las solicitudes.'),
						
					),
					
					array(
						
							'type' => 'text',
						
							'label' => $this->l('Lista de dominios autorizados'),
						
							'name' => 'callmeplease_domains',
						
							'desc' => $this->l('Si el campo anterior est� vac�o y este es el dominio encargado de centralizar las solicitudes, rellene el siguiente campo con una lista de los dominios autorizados, separados por coma'),
					
					),

				),

				'submit' => array(

					'title' => $this->l('Save'),

				)

			),

		);



		$helper = new HelperForm();

		$helper->show_toolbar = false;

		$helper->table =  $this->table;

		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

		$helper->default_form_language = $lang->id;

		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

		$this->fields_form = array();



		$helper->identifier = $this->identifier;

		$helper->submit_action = 'submitModule';

		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

		$helper->token = Tools::getAdminTokenLite('AdminModules');

		
		$config_values = $this->getConfigFieldsValues();
		
		
		if (!Tools::isSubmit('submitModule'))
		
		{
			if (empty($config_values['callmeplease_admin_folder'])){
				$default_admin_folder =  _PS_ADMIN_DIR_;
				$last_backslash_position = strrpos ( $default_admin_folder , "/");
				$default_admin_folder = substr($default_admin_folder, $last_backslash_position+1);
				$config_values['callmeplease_admin_folder'] = $default_admin_folder;
			}
			
		}
		
		
		$helper->tpl_vars = array(

			'fields_value' => $config_values,

			'languages' => $this->context->controller->getLanguages(),

			'id_language' => $this->context->language->id

		);



		return $helper->generateForm(array($fields_form));

	}



	public function getConfigFieldsValues()

	{

		return array(

			'blockcontact_telnumber' => Tools::getValue('blockcontact_telnumber', Configuration::get('BLOCKCONTACT_TELNUMBER')),
			
			'blockcontact_showntelnumber' => Tools::getValue('blockcontact_showntelnumber', Configuration::get('BLOCKCONTACT_SHOWNTELNUMBER')),

			'blockcontact_email' => Tools::getValue('blockcontact_email', Configuration::get('BLOCKCONTACT_EMAIL')),
			
			'blockcontact_callmeplease_activated' => Tools::getValue('blockcontact_callmeplease_activated', Configuration::get('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED')),

			'callmeplease_host' => Tools::getValue('callmeplease_host', Configuration::get('CALLMEPLEASE_HOST')),
				
			'callmeplease_admin_folder' => Tools::getValue('callmeplease_admin_folder', Configuration::get('CALLMEPLEASE_ADMIN_FOLDER')),
				
			'callmeplease_domains' => Tools::getValue('callmeplease_domains', Configuration::get('CALLMEPLEASE_DOMAINS'))
		);

	}
	
	public function hookDisplayBackOfficeHeader(){
		// die('añadir js para backoffice');
		
		$this->context->controller->addCSS(($this->_path).'css/blockcontactadminmanu.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/blockcontactadminmanu.js');
		
		$this->context->controller->addJqueryUI('ui.dialog');
		$this->context->controller->addJqueryUI('ui.draggable');
		$this->context->controller->addJqueryUI('ui.resizable');
		
		$this->context->controller->addJqueryUI('ui.effect');
		$this->context->controller->addJqueryUI('ui.effect-explode');
		$this->context->controller->addJqueryUI('ui.effect-blind');

	}
	
	
	public function hookDisplayBackOfficeTop($params){
		// die('añadir el tpl callList.tpl. PEnsar si este puede cachearse, probablemente no');
		global $smarty;
		/*
		$callMePleaseHost = Configuration::get('CALLMEPLEASE_HOST');
		if (substr($callMePleaseHost, -1) == '/'){
			$callMePleaseHost = substr($callMePleaseHost, 0, -1);
		}
		$_admin_folder = Configuration::get('CALLMEPLEASE_ADMIN_FOLDER');
		if (substr($_admin_folder, -1) == '/'){
			$_admin_folder = substr($_admin_folder, 0, -1);
		}
		*/
		// $path = $this->context->link->getModuleLink('blockcontactmanu','AdminCallmePleaseController');
		/*
		$path = $this->context->link->getAdminLink('AdminCallmePlease');
		if (!empty($callMePleaseHost) && (!empty($_admin_folder))){
			$fullCallMePath = $callMePleaseHost.'/'.$_admin_folder.'/'.$path;
		}
		else {
			$fullCallMePath = $path;
		}
		*/
		
		$employee = $this -> context -> employee -> firstname . ' ' . $this -> context -> employee -> lastname;
		
		$fullCallMePath = $this -> getAjaxControllerPath();

		
		$sound_url = _MODULE_DIR_.$this->name."/callmeplease.mp3";
		
		$tpl = 'callList';

		if (isset($params['callList_tpl']) && $params['callList_tpl'])

			$tpl = $params['callList_tpl'];

		// if (!$this->isCached($tpl.'.tpl', $this->getCacheId())) NO PODEMOS CACHEAR PUES VA A SER DINAMICO

			if (empty($callMePleaseHost)) { // está vacio ==> nuestro host es el host que ejecuta este archivo
				$pending_calls_array = $this->getPendingInquiryCalls();
			}
			else { // las llamadas se cargarán con el primer request xhr
				$pending_calls_array = null;
			}
			
			$cookie = new Cookie("callmeplease");
			if (isset($cookie->sonido_llamadas)){
				if ($cookie->sonido_llamadas == 'true'){
					$sonido_activado = 'true';
				}
				else {
					$sonido_activado = 'false';
				}
			}
			else {
				$sonido_activado = 'true';
			}
			
			// la siguiente linea creo que no se usa dentro del tpl, se deja por si acaso
			// @Deprecated
			$callmepleaseLineTPL = _PS_MODULE_DIR_ . $this -> name . '/views/templates/admin/callListLine.tpl';
			
			$activation = $this->checkCallmepleaseActivation();
			
			$smarty->assign(array(
				'callmeplease_activated' => $activation,
				'pending_calls' => $pending_calls_array,
				'callmeplease_path' => $fullCallMePath,
				'alert_sound_url' => $sound_url,
				'sonido_activado' => $sonido_activado,
				'callmepleaseLineTPL' => $callmepleaseLineTPL,
				'employee' => $employee
			));
			
		Tools::clearCache(null, $tpl.'.tpl');
		return $this->display(__FILE__, $tpl.'.tpl');
	}
    
	public function getPendingInquiryCalls(){
		
		
		$host = Configuration::get('CALLMEPLEASE_HOST');
		
		// $admin_folder = Configuration::get('CALLMEPLEASE_ADMIN_FOLDER');
		
		if (!empty($host) /* && !empty($admin_folder)*/){
			
			// $host_url = $this->getCallControllerLink();
			$host_url = $this->getAjaxControllerPath();
			
			$options = "?action=getInquiries&ajax=true";
			
			// we strip protocol or relative protocol
			$end_protocol_position = strpos ( $host_url , "//");
			if ($end_protocol_position !== false){ // Changing protocol to relative
				$host_url = substr($host_url, $end_protocol_position+mb_strlen("//"));
			}
			
			// var_dump($host_url);
			
			// create curl resource
			$ch = curl_init();
				
			// set url
			curl_setopt($ch, CURLOPT_URL, $host_url.$options);
				
			//return the transfer as a string
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
			// $output contains the output string
			$output = curl_exec($ch);
				
			// close curl resource to free up system resources
			curl_close($ch);
			
			$decoded_result = json_decode($output);
			if ($decoded_result -> flag == 1){
				return $decoded_result -> callList;
			}
			else {
				return null;
			}
				
		}
		else {
			$callmepleasedata_collection = new PrestashopCollection('callmepleasedata');
			
			// where($field, $operator, $value, $method = 'where')
			// $callmepleasedata_data_collection->where('ip', '=', $ip);
			
			$callmepleasedata_collection->where('inquiry_status' , '!=', 'a');
			$inquiryDataList = $callmepleasedata_collection->getResults();
			// die(json_encode($inquiryDataList));
			// 'inquiry_admin_code' => $this->getInquiryCode($inquiryData)
			foreach ($inquiryDataList as $inquiryData){
				$code = $this->getInquiryCode($inquiryData);
				// die($code);
				$inquiryData -> inquiry_admin_code = $code;
				$inquiryData -> hash = md5(serialize($code));
			}
			
	        return $inquiryDataList;
		}
    }
        
    public function countPendingCalls(){
        return count($this->getPendingInquiryCalls());
    }
	
	public function isBannedByIP($ip){
		$callmepleasedata_data_collection = new PrestashopCollection('callmepleasedata_data');
		
		// where($field, $operator, $value, $method = 'where')
		// $callmepleasedata_data_collection->where('ip', '=', $ip);
		
		/* baneo por sesion => ban_method = 's'
		 * baneo por ip => ban_method = 'i'
		 */
		$callmepleasedata_data_collection->sqlWhere("`ip` = '$ip' AND `ban_method` = 'i'");
		$data = $callmepleasedata_data_collection->getResults();
		if (!empty($data) > 0) {
			return true;
		}
		else return false;
	}
	
	public function isBannedBySession($session_id){
		$callmepleasedata_data_collection = new PrestashopCollection('callmepleasedata_data');
		
		// where($field, $operator, $value, $method = 'where')
		// $callmepleasedata_data_collection->where('ip', '=', $ip);
		
		/* baneo por sesion => ban_method = 's'
		 * baneo por ip => ban_method = 'i'
		 */
		$callmepleasedata_data_collection->sqlWhere("`session_id` = '$session_id' AND `ban_method` = 's'");
		$data = $callmepleasedata_data_collection->getResults();
		if (!empty($data) > 0) {
			return true;
		}
		else return false;
	}
	
	public function isBanned($ip, $session_id){
		return ($this->isBannedByIP($ip) || $this->isBannedBySession($session_id));
	}

	public function unatendedCallInquiryExists($customer_name, $customer_phone, $customer_country){
		$callmepleasedata_collection = new PrestashopCollection('callmepleasedata');
		// inquiry_status != 'a' -> distinto a atendido: unnatended or in progress
		$callmepleasedata_collection -> sqlWhere("`customer_name` = '$customer_name' AND `customer_phone` = '$customer_phone' AND `customer_country` = '$customer_country' AND `inquiry_status` != 'a'");
		$existing_data = $callmepleasedata_collection->getResults();
		return $existing_data;
	}
	
	public function insertNewCallInquiry($customer_name, $customer_phone, $customer_country){
		
		$inserted = false;
		$existing_data = $this -> unatendedCallInquiryExists($customer_name, $customer_phone, $customer_country);
		if (empty($existing_data)) { // solo lo ejecutamos si no existe información previa a este customer name, phone y country asociada
			try {
				
				$callmepleasedata = new callmepleasedata();
				$callmepleasedata->customer_name = $customer_name;
				$callmepleasedata->customer_phone = $customer_phone;
				$callmepleasedata->customer_country = $customer_country;
				$callmepleasedata->inquiry_status = 'u';
				
				$session_id = session_id();
				
				$ip = $this->get_client_ip();
				
				$host = $_SERVER['HTTP_ORIGIN']; // empty if same domain in firefox
				if (empty($host)){
					$host = $_SERVER['HTTP_HOST']; // firefox: no cross domain, we can use http_host
				}
				// we strip protocol or relative protocol
				$end_protocol_position = strpos ( $host , "//");
				if ($end_protocol_position !== false){ // Changing protocol to relative
					$host = substr($host, $end_protocol_position+mb_strlen("//"));
				}
				
				$callmepleasedata->origin_url = $host;
				
				$inserted = false;
				
				if (!$this->isBanned($ip, $session_id)){ // NO ESTA BANEADO SI ENTRA AQUI
					
					$callmepleasedata_data_collection = new PrestashopCollection('callmepleasedata_data');
					
					// where($field, $operator, $value, $method = 'where')
					$callmepleasedata_data_collection->where('session_id', '=', $session_id);
					$data = $callmepleasedata_data_collection->getResults();
					
					if ((!empty($data))&&(count($data) != 0)){
						if ($ip == $data[0]->ip){
							
							$callmepleasedata->id_data = $data[0]->id_data;
							$inserted = $callmepleasedata->save();
							
						}
						else {
							
							$callmepleasedata_data = new callmepleasedata_data();
							$callmepleasedata_data->session_id = $session_id;
							$callmepleasedata_data->ip = $ip;
							$callmepleasedata_data->ban_method = 'n';
							$callmepleasedata_data->save();
							
							// return $callmepleasedata_data->id;
							
							$callmepleasedata->id_data = $callmepleasedata_data->id;
							$inserted = $callmepleasedata->save();
							
						}		
					}
					else {
						
						$callmepleasedata_data = new callmepleasedata_data();
						$callmepleasedata_data->session_id = $session_id;
						$callmepleasedata_data->ip = $ip;
						$callmepleasedata_data->ban_method = 'n';
						$callmepleasedata_data->save();
						$callmepleasedata->id_data = $callmepleasedata_data->id;
						$inserted = $callmepleasedata->save();
						
					}
					
				}  
			}
			catch (Exception $e){
				return $e->getMessage();
			}
		}
		return $inserted;
    }
	
	public function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	public function checkIfBeingAttended($inquiry_id){
		// Haciendolo sin colecciones:
		$callmepleasedata = new callmepleasedata($inquiry_id);
		return $callmepleasedata->inquiry_status == 'p'; // in progress
	}
	
	public function callInquiryHasBeenAttended($inquiry_id){
		$callmepleasedata_collection = new PrestashopCollection('callmepleasedata');
		
		// where($field, $operator, $value, $method = 'where')
		// $callmepleasedata_data_collection->where('ip', '=', $ip);
		
		$callmepleasedata_collection->where('inquiry_ID' , '=', $inquiry_id);
		$data = $callmepleasedata_collection->getResults();
		if (count($data) > 0) {
			/* inquiry_status:
				u -> unattended; 
				p -> in progress (being attended right now); 
				a -> already attended; 
			 * max session id size: 40 chars
			 */
			$data[0]->inquiry_status = 'a';
			$result = $data[0]->save();
			return $result;
		}
		else throw new RuntimeException("No inquiry found with id: $inquiry_id. Exception launched in callInquiryHasBeenAttended");
	}
	
	public function callInquiryIsBeingAttended($inquiry_id, $employee){
		$callmepleasedata_collection = new PrestashopCollection('callmepleasedata');
		
		// where($field, $operator, $value, $method = 'where')
		// $callmepleasedata_data_collection->where('ip', '=', $ip);
		
		$callmepleasedata_collection->where('inquiry_ID' , '=', $inquiry_id);
		$data = $callmepleasedata_collection->getResults();
		if (count($data) > 0) {
			/* inquiry_status:
				u -> unattended; 
				p -> in progress (being attended right now); 
				a -> already attended; 
			 * max session id size: 40 chars
			 */
			$data[0]->inquiry_status = 'p';
			$data[0]->attended_by = $employee;
			$result = $data[0]->save();
			return $result;
		}
		else throw new RuntimeException("No inquiry found with id: $inquiry_id. Exception launched in callInquiryIsBeingAttended");
	}
	
	public function checkInquiryStatuses($id_inquiry_list){
        if(!empty($id_inquiry_list)){
            $result = array();
            for ($i = 0; $i < count($id_inquiry_list); $i++){
				$inquiryData = new callmepleasedata($id_inquiry_list[$i]);
								
				if ($inquiryData->inquiry_status != 'u'){ // unattended: ya ha sido atendida o lo est� siendo ahora mismo
						
					$code = $this->getInquiryCode($inquiryData);
					$hash = md5(serialize($code));
					
					// die(json_encode("aqui entra"));
					
					$result[] = array ('inquiry_ID' => $id_inquiry_list[$i], 
										'inquiry_status' => $inquiryData->inquiry_status,
										'inquiry_admin_code' => $code,
										'hash' => $hash
					);
				}
            }
        }
        return $result;
    }
    
    
    public function getInquiryCode($inquiry_object){
    	
    	global $smarty;
    	
    	$inquiryData = new callmepleasedata_data($inquiry_object->id_data);
    	 
    	$tpl = 'callListLine';
    	
    	if (isset($params['callListLine_tpl']) && $params['callListLine_tpl']) {
    		$tpl = $params['callListLine_tpl'];
    	}
    	
    	// $fullCallMePath = $this->getCallControllerLink();
    	$fullCallMePath = $this->getAjaxControllerPath();
    	$callmepleasecomments_path = $this->getCommentControllerLink();
    	$callmeplease_edit_path = $this -> getCallControllerLink();
    	// $callmeplease_edit_path = "//google.es";
    	
    	$commentOBJ = $this->getCommentObject($inquiry_object);
    	
    	$is_being_attended = $this->checkIfBeingAttended($inquiry_object->id);
    	if ($is_being_attended == true) {
    		//$employee = new Employee($inquiry_object->attended_by);
    		//$employee_data = $employee->firstname.' '.$employee->lastname;
    		$employee_data = $inquiry_object->attended_by;
    	}
    	
    	$smarty->assign(array(
    			'comment_token' => Tools::getAdminTokenLite('AdminCallmePleaseComments'),
    			'call_token' => Tools::getAdminTokenLite('AdminCallmePlease'),
    			'callmepleasecomments_path' => $callmepleasecomments_path,
    			'solicitud_llamada' => $inquiry_object,
    			'callmeplease_path' => $fullCallMePath,
    			'comentarios' => $commentOBJ,
    			'inquiry_data' => $inquiryData,
    			'employee' => $employee_data,
    			'callmeplease_edit_path' => $callmeplease_edit_path
    	));
    	
    	return $this->display(__FILE__, 'views/templates/admin/'. $tpl.'.tpl');
    }
    
    /**
     * Removes the token parameter from an admin link
     * due to the fact that we are going to use
     * this link between different domain names
     * 
     * @param unknown $adminLink the string of the admin link
     * 
     * @throws Exeption if an empty admin link is passed
     * 
     * @return mixed the admiin link without the token parameter
     * 
     */
    private function removeToken($adminLink){
    	if (empty($adminLink)){
    		throw new Exeption("Could not remove token if admin link is null");
    	}
    	$token_position = strpos ( $adminLink , "&token");
    	if ($token_position !== false){ // Changing protocol to relative
    		$adminLink = substr($adminLink, 0, $token_position);
    	}
    	return $adminLink;
    }
    
    public function getCallControllerLink(){
    	
    	$callMePleaseHost = Configuration::get('CALLMEPLEASE_HOST');
    	
    	if (empty($callMePleaseHost)) {
    		$callMePleaseHost = _PS_BASE_URL_.__PS_BASE_URI__;
    	}
    	
    	if (substr($callMePleaseHost, -1) == '/'){
    		$callMePleaseHost = substr($callMePleaseHost, 0, -1);
    	}
    	$_admin_folder = Configuration::get('CALLMEPLEASE_ADMIN_FOLDER');
    	if (substr($_admin_folder, -1) == '/'){
    		$_admin_folder = substr($_admin_folder, 0, -1);
    	}
    	// $path = $this->context->link->getModuleLink('blockcontactmanu','AdminCallmePleaseController');
    	$path = $this->context->link->getAdminLink('AdminCallmePlease');
    	// $path = $this -> removeToken($path);
    	if (!empty($callMePleaseHost) && (!empty($_admin_folder))){
    		$fullCallMePath = $callMePleaseHost.'/'.$_admin_folder.'/'.$path;
    		
    		$end_protocol_position = strpos ( $fullCallMePath , "//");
    		if ($end_protocol_position !== false){ // Changing protocol to relative
    			$fullCallMePath = substr($fullCallMePath, $end_protocol_position);
    		}
    		else { // el protocolo no se ha especificado en el host destino, añadimos protocolo relativo
    			$fullCallMePath = '//'.$fullCallMePath;
    		}
    		
    	}
    	else {
    		$fullCallMePath = $path;
    	}
    	// die($fullCallMePath);
    	return $fullCallMePath;
    }
    
    
    public function getCommentControllerLink(){
    	$callMePleaseHost = Configuration::get('CALLMEPLEASE_HOST');
    	
    	if (empty($callMePleaseHost)) {
    		$callMePleaseHost = _PS_BASE_URL_.__PS_BASE_URI__;
    	}
    	if (substr($callMePleaseHost, -1) == '/'){
    		$callMePleaseHost = substr($callMePleaseHost, 0, -1);
    	}
    	$_admin_folder = Configuration::get('CALLMEPLEASE_ADMIN_FOLDER');
    	if (substr($_admin_folder, -1) == '/'){
    		$_admin_folder = substr($_admin_folder, 0, -1);
    	}
    	// $path = $this->context->link->getModuleLink('blockcontactmanu','AdminCallmePleaseController');
    	$path = $this->context->link->getAdminLink('AdminCallmePleaseComments');
    	// $path = $this -> removeToken($path);
    	if (!empty($callMePleaseHost) && (!empty($_admin_folder))){
    		$fullCallMeCommentPath = $callMePleaseHost.'/'.$_admin_folder.'/'.$path;		
    		
    		$end_protocol_position = strpos ( $fullCallMeCommentPath , "//");
    		if ($end_protocol_position !== false){ // Changing protocol to relative
    			$fullCallMeCommentPath = substr($fullCallMeCommentPath, $end_protocol_position);
    		}
    		else { // el protocolo no se ha especificado en el host destino, añadimos protocolo relativo
    			$fullCallMeCommentPath = '//'.$fullCallMeCommentPath;
    		}
    		
    	}
    	else {
    		$fullCallMeCommentPath = $path;
    	}
    	// die($fullCallMeCommentPath);
    	return $fullCallMeCommentPath;
    }

    
    public function insertComment($comment, $insertionMethodArr, $id_inquiry){
    	
    	
    	$data = $this->getDataByInquiryID($id_inquiry);
    	$callmePlease = new callmepleasedata($id_inquiry);
    	
    	$callmepleasecommentsdata = new callmepleasecommentsdata();
    	
    	if(!empty($insertionMethodArr['customer_phone'])) {
    		$callmepleasecommentsdata->customer_phone = $callmePlease->customer_phone;
    	}
    	else {
    		$callmepleasecommentsdata->customer_phone = null;
    	}
    	if(!empty($insertionMethodArr['session_id'])) {
    		$callmepleasecommentsdata->session_id = $data->session_id;
    	}
    	else {
    		$callmepleasecommentsdata->session_id = null;
    	}
    	if(!empty($insertionMethodArr['ip'])) {
    		$callmepleasecommentsdata->ip = $data->ip;
    	}
    	else {
    		$callmepleasecommentsdata->ip = null;
    	}
    	
    	$callmepleasecommentsdata->comment = $comment; // este campo es obligatorio
    		
    	return $callmepleasecommentsdata->save(true); // true => null values are allowed in the sql sentence
    }
    
    private function getDataByInquiryID($id_inquiry){
    	$callmePlease = new callmepleasedata($id_inquiry);
    	$callmePlease_data = new callmepleasedata_data($callmePlease->id_data);
    	return $callmePlease_data;
    }
    
    private function getCommentObject($inquiry_object){
    	
    	$data = $inquiry_object;
    	$callmePleaseData = new callmepleasedata_data($data->id_data);
    	 
    	$dataStructure = array();
    	 
    	
    	$dataStructure['customer_phone'] = $data->customer_phone;
    	$dataStructure['session_id'] = $callmePleaseData->session_id;
    	$dataStructure['ip'] = $callmePleaseData->ip;
    	
    	
    	
    	$comment_collection_ps_object = new PrestashopCollection('callmepleasecommentsdata');
    	
    	$restrictions_found = array();
    	
    	$sqlWhere = "FALSE";
    	if (!empty($dataStructure['customer_phone'])) {
    		$sqlWhere .= " OR `customer_phone` = '{$dataStructure['customer_phone']}'";
    		
    	}
    	if (!empty($dataStructure['session_id'])) {
    		$sqlWhere .= " OR `session_id` = '{$dataStructure['session_id']}'";
    		
    	}
    	if (!empty($dataStructure['ip'])) {
    		$sqlWhere .= " OR `ip` = '{$dataStructure['ip']}'";
    		
    	}
    	
    	$comment_collection_ps_object->sqlWhere($sqlWhere);
    	$comment_data = $comment_collection_ps_object->getResults();
    	
    	$i = 0;
    	foreach ($comment_data as $comment) {
    		
    		if ((!empty($restrictions_found['customer_phone']))&&($restrictions_found['customer_phone'] == true) 
    				&&(!empty($restrictions_found['session_id']))&& ($restrictions_found['session_id'] == true) 
    				&&(!empty($restrictions_found['ip']))&& ($restrictions_found['ip'] == true)) {
    					
    			$restrictions_found[$i] = null;
    			$i++;
    			break;
    		}
    		
    		if (!empty($comment->customer_phone)) {
    			$restrictions_found[$i]['customer_phone'] = true;
    		}
    		if (!empty($comment->session_id)) {
    			$restrictions_found[$i]['session_id'] = true;
    		}
    		if (!empty($comment->ip)) {
    			$restrictions_found[$i]['ip'] = true;
    		}
    		$i++;
    	}
    	
    	if (!empty($comment_data) > 0) {
    		$commentObject = new stdClass(); // objeto gen�rico
    		$commentObject -> restrictions_found = $restrictions_found;
    		$commentObject -> comment_collection = $comment_data;
    		return $commentObject;
    	}
    	else return false;
    	
    }
    
    public function isCrossOriginAllowed(){
    	$allowedDomains = strtolower(Configuration::get('CALLMEPLEASE_DOMAINS'));
    	$allowedDoainsArr = explode(',', $allowedDomains);
    	
    	$_origin = strtolower($_SERVER['HTTP_ORIGIN']);
    	
    	foreach ($allowedDoainsArr as $host) {
    		if (strpos($_origin, $host) !== false){
    			return true;
    		}
    	}
    	
    }
    
}

