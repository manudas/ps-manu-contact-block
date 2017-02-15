<?php

	class AdminRateByDestinyConfigPriceRulesController extends ModuleAdminController{
	 
		public function __construct(){		 
			// $this->colorOnBackground = true;
			$this->addRowAction('edit'); //add an edit button
			$this->addRowAction('delete'); //add a delete button
			$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
			$this->explicitSelect = true;
			$this->context = Context::getContext();
			$this->id_lang = $this->context->language->id;
			$this->path = _MODULE_DIR_."pricerules";
		 
			$this->default_form_language = $this->context->language->id;
			$this->table = 'PRICE_RULES_RATE_BY_CITY'; //define the main table
			$this->className = 'RateByDestinyPriceRulesData'; //define the module entity. name de la clase q contiene la definition de base de datos
			$this->identifier = "ID_PK"; //the primary key
			//die(var_dump($this->id));
			//then define select part of the query
			$langID = $this->context->language->id;
			//$this->_select = 'a.ID_RATE,a.NAME';
		/*
				al.name
				';
		*/ 
			//join to an existing table if you need some extra informations
			/*		
				public $ID_PK;
				public $ID_CITY;
				public $ID_RATE;
			*/
			
			$this->_join= '
				INNER JOIN `'._DB_PREFIX_.'PRICE_RULES_CITY` city ON (city.`ID_CITY` = a.`ID_CITY`)
				INNER JOIN `'._DB_PREFIX_.'PRICE_RULES_RATES` rate ON (rate.`ID_RATE` = a.`ID_RATE`)
				INNER JOIN `'._DB_PREFIX_.'country_lang` cl ON (city.`ID_COUNTRY` = cl.`id_country`)
				INNER JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = city.`ID_STATE`)	';
			
			
			$this->_where = '
				AND cl.`id_lang` ="'. $langID.'"';
			
			
			//and define the field to display in the admin table
			parent::__construct();
		}
		
		
		public function renderList(){
			$this->fields_list = array(
										'ID_PK' => array(
												'type'  => 'text',
												'title' => $this->l('ID'),
												'align' => 'center',
												'width' => 20
										),
										'COUNTRY' => array(
												'type'  => 'text',
												'title' => $this->l('País'),
                                                                                                'default_value' => 6,
												'width' => 'auto',
												'align' => 'center',
												'filter_key' => 'cl!NAME'
										),			
										'STATE_NAME' => array(
												'type'  => 'text',
												'title' => $this->l('Provincia'),
												'align' => 'center',
												'width' => 'auto',
												'filter_key' => 's!name'
										),
										'CITY' => array(
												'type'  => 'text',
												'title' => $this->l('Ciudad'),
												'align' => 'center',
												'width' => 'auto',
												'filter_key' => 'city!NAME'
										),
										'RATE' => array(
												'type' => 'text',
												'title' => $this->l('Zona'),
												'align' => 'center',
												'width' => 'auto',
												'filter_key' => 'rate!NAME'
										)										

								);
								
								
								
			if (Tools::isSubmit($this->table."Orderby")){
				$this->_orderBy = Tools::getValue($this->table."Orderby");
				$this->_orderWay = Tools::getValue($this->table."Orderway");
			}			
									
								
								
								
			if (Tools::isSubmit('submitFilter'))
			{
				$whereAdition = '';
				foreach ($this->fields_list AS $field => $t)
				{
					if (isset($t['filter_key']))
						$field = $t['filter_key'];
					if ($val = Tools::getValue($this->table.'Filter_'.$field))
					{
						if(!is_array($val) && !empty($val)){
							$replacedCharField = str_replace('!','.',$field);
							$whereAdition .= $whereAdition ?  " AND `".$replacedCharField."` like '%".$val."%'" : " `".$replacedCharField."` like '%".$val."%'";
						}
						elseif(is_array($val) && !empty($val))
						{
							$tmp = '';
							$replacedCharField = str_replace('!','.',$field);
							if ((count($val) == 2) && !empty($val[0]) && !empty($val[1])){ // ¿Filtro es entre dos fechas ?
								$tmp = " `".$replacedCharField."` BETWEEN '".$val[0]."' AND '".$val[1]."'";
							}
							else { // ¿otro tipo de filtro ?
								foreach($val as $v)
									if(!empty($v))
										$tmp .= $tmp ?  " OR `".$replacedCharField."` like '%".$v."%'" : " `".$replacedCharField."` like '%".$v."%'";	
							}
							if(Tools::strlen($tmp))
							{
							
								$tmp = $whereAdition ? " AND (".$tmp.")" : "(".$tmp.")";
								$whereAdition .= $tmp;

							}
						}
					}
				}

				$this->_where .= $whereAdition ? " AND ".$whereAdition : "";
				

			}								
								
								
								
			return parent::renderList();
		}
				
		public function renderForm(){
			$langID = $this->context->language->id;
			
			$rateCollection = new Collection('RatesPriceRulesData');
			$inflatedRateCollection = $rateCollection->getAll();
			
			$arrayRateOptions = array();
			foreach ($inflatedRateCollection as $clave => $rateObj){
				$id_rate = $rateObj->ID_RATE;
				//die(var_dump($categoryObj->name));
				$rate_name = $rateObj->NAME;
				$arrayRateOptions[$clave] = array('id' => $id_rate, 'option' => $rate_name);
			}
			
			$countryCollection = new Collection('Country');
			$inflatedCountryCollection = $countryCollection->getAll();
			//die(var_dump($countryCollection));
			
			/*	public $id;
			public $name;
			public $id_state;
			public $active;
			*/

			$arrayCountriesOptions = array();
			foreach ($inflatedCountryCollection as $clave => $countryObj){
				$id_country = $countryObj->id;
				$country_name = $countryObj->name[$langID];
				$arrayCountriesOptions[$clave] = array('id' => $id_country, 'option' => $country_name);
			}
			//die(var_dump($arrayCountriesOptions));
			
			$arrayStatesOptions = null;
			// die(var_dump($this->object));
			if (isset($this->object->ID_COUNTRY)){
				$id_country = $this->object->ID_COUNTRY;
				$arrayStatesOptions = array();
				$stateCollection = new Collection('State');
				$stateCollection->where('id_country', '=', $id_country, 'where');
				$inflatedStateCollection = $stateCollection->getAll();
                                
				
				foreach ($inflatedStateCollection as $clave => $stateObj){
					$id_state = $stateObj->id;
					$state_name = $stateObj->name;
					$arrayStatesOptions[$clave] = array('id' => $id_state, 'option' => $state_name);				
				}
				//die(var_dump($arrayStatesOptions));
			}
			
			$arrayCitiesOptions = null;
			if (isset($this->object->ID_STATE)){
				$id_state = $this->object->ID_STATE;
				$id_country = $this->object->ID_COUNTRY;

				$arrayCitiesOptions = array();
				
				// ATENSION: CREAMOS DEFINITION PARA CITY VERDAD? SI LO HASEMOS POEMOS USAR LAS COLESTION. SINO POS NO
				
				$cityCollection = new Collection('CitiesPriceRulesData');
				$cityCollection->where('ID_STATE', '=', $id_state, 'where');
				$cityCollection->where('ID_COUNTRY', '=', $id_country, 'where');
				$inflatedCityCollection = $cityCollection->getAll();
				
				foreach ($inflatedCityCollection as $clave => $cityObj){
					$id_city = $cityObj->id;
					$city_name = $cityObj->NAME;
					$arrayCitiesOptions[$clave] = array('id' => $id_city, 'option' => $city_name);				
				}
				//die("id country: ".$id_country." id state: ".$id_state." array de cities ".var_dump($arrayCitiesOptions));
			}			

			//if (Tools::isSubmit('add'.$this->table)){
			
			//define the field to display with the form helper
			
				$this->fields_form = array(
										'tinymce' => true,
										'legend' => array(
															'title' => $this->l('Asignar zonas a destinos')
													),
										'input' => array( 
														array(
															'type' => 'hidden',
															'readonly' => true,																													
															'name' => 'actionUrl',													
															'required' => true,
														),
														array(
															'type' => 'select',
															'options' => array (
																			'query' => $arrayCountriesOptions,
																			'id' => 'id', /* el segundo id identifica el indice que almacena el value del option. el primero es el indice de id en el primer array */
																			'name' => 'option'),															
															'label' => $this->l("País")." :",
															'name' => 'ID_COUNTRY',
                                                                                                                        'default_value' => 6,
															'size' => 'auto',
															'required' => true,
															'hint' => $this->l('País de la ciudad a la que se va a asignar un catálogo')
														),

														array(
															'type' => 'select',
															'options' => array (
																			'query' => $arrayStatesOptions,
																			'id' => 'id', /* el segundo id identifica el indice que almacena el value del option. el primero es el indice de id en el primer array */
																			'name' => 'option'),															
															'label' => $this->l("Seleccione la provincia")." :",
															'name' => 'ID_STATE',
															'size' => 'auto',
															'required' => true,
															'hint' => $this->l('Provincia a la que pertenece la ciudad a la que se va a asignar un catálogo')
														),
														array(
															'type' => 'select',
															'options' => array (
																			'query' => $arrayCitiesOptions,
																			'id' => 'id', /* el segundo id identifica el indice que almacena el value del option. el primero es el indice de id en el primer array */
																			'name' => 'option'),															
															'label' => $this->l("Seleccione la ciudad")." :",
															'name' => 'ID_CITY',
															'size' => 'auto',
															'required' => true,
															'hint' => $this->l('Ciudad a la que se va a asignar una zona')
														),
														array(
															'type' => 'select',
															'options' => array (
																			'query' => $arrayRateOptions,
																			'id' => 'id', /* el segundo id identifica el indice que almacena el value del option. el primero es el indice de id en el primer array */
																			'name' => 'option'),															
															'label' => $this->l("Zona a asignar")." :",
															'name' => 'ID_RATE',
															'size' => 'auto',
															'required' => true,
															'hint' => $this->l('Seleccione la zona que va a asignar a la ciudad')
														),
														
														array(
															'type'      => 'radio',                               // This is an <input type="checkbox"> tag.
															'label'     => $this->l('Seleccione si aplicar a toda la provincia, país, o solo ciudad'),        // The <label> for this <input> tag.
															'desc'      => $this->l('Seleccione donde aplicar'),   // A help text, displayed right next to the <input> tag.
															'name'      => 'SCOPE',                              // The content of the 'id' attribute of the <input> tag.
															'required'  => true,                                  // If set to true, this option must be set.
															'default'	=> 0,
															'class'     => 't',                                   // The content of the 'class' attribute of the <label> tag for the <input> tag.
															'is_bool'   => false,                                  // If set to true, this means you want to display a yes/no or true/false option.
																												// The CSS styling will therefore use green mark for the option value '1', and a red mark for value '2'.
																												// If set to false, this means there can be more than two radio buttons,
																												// and the option label text will be displayed instead of marks.
															'values'    => array(                                 // $values contains the data itself.
																array(
																	'id'    => 'scope_city',                           // The content of the 'id' attribute of the <input> tag, and of the 'for' attribute for the <label> tag.
																	'value' => 0,                                     // The content of the 'value' attribute of the <input> tag.   
																	'label' => $this->l('Aplicar solo a ciudad')                    // The <label> for this radio button.
																),
																array(
																	'id'    => 'scope_state',
																	'value' => 1,
																	'label' => $this->l('Aplicar a toda la provincia')
																),
																array(
																	'id'    => 'scope_country',
																	'value' => 2,
																	'label' => $this->l('Aplicar a todo el país')
																)
															)
														)
										)
									);
									//add the save button
				$this->fields_form['submit'] = array(
											'title' => $this->l('Guardar'),
											'class' => 'button'
									);
				//$this->action = 'prueba';
			//}
			
			if (!($MyModuleObject = $this->loadObject(true)))
				return;
			//populate the field with good values if we are in an edition
			foreach($this->fields_form["input"] as $inputfield){
				$this->fields_value[$inputfield["name"]] = $MyModuleObject->$inputfield["name"];
			}
			$actionURL = $this->context->link->getAdminLink('AdminAjaxPricerules',true);
			$this->fields_value['actionUrl'] = $actionURL;
			/*
			$this->context->smarty->assign(array(
				'mymodule_controller_url' => $this->context->link->getAdminLink('AdminModifiersConfigPriceRules'),//give the url for ajax query
			));
		*/
			//$more = $this->module->display($path, 'view/mymodule.tpl');
		 
		 	$form = parent::renderForm();

			return $form;
		}
		
		public function postProcess()
		{
			$result = false;
			//$ModifiersAssignPriceRulesData = new ModifiersAssignPriceRulesData();
			if (Tools::isSubmit('submitAdd'.$this->table))
			{
				$id_country = Tools::getValue('ID_COUNTRY');
				$id_state = Tools::getValue('ID_STATE');
				$id_city = Tools::getValue('ID_CITY');
				$ambito_de_aplicacion = Tools::getValue('SCOPE');
				$rate = Tools::getValue('ID_RATE');
				/* AMBITO DE APLICACION:
				 * 0 - solo la ciudad seleccionada
				 * 1 - el estado / provincia seleccionado
				 * 2 - el país completo seleccionado
				 */
				switch ($ambito_de_aplicacion){
					case 0:
						$result = $this->SetRateFor($rate, $id_country, $id_state, $id_city); 
					break;
					case 1:
						$result = $this->SetRateFor($rate, $id_country, $id_state, null); 
					break;
					case 2:
						$result = $this->SetRateFor($rate, $id_country, null, null); 
					break;
				}	
				if (!$result){
					// die("aqui entra con errores");
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t add/update the current object/s');
				}
				
			}
			elseif (Tools::isSubmit('delete'.$this->table)){

				$id = Tools::getValue('ID_PK');

				$RateByDestinyPriceRulesData = new RateByDestinyPriceRulesData();
				
				$RateByDestinyPriceRulesData->id = $id;
				$RateByDestinyPriceRulesData->ID_PK = $id;

				$result = $RateByDestinyPriceRulesData->delete();
				
				if (!$result){
					// die("aqui entra con errores");
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t delete the current object');
				}
				
			}
			elseif (Tools::isSubmit('submitBulkdelete'.$this->table)){ 
				$result = true;
				$RateByDestinyPriceRulesData = new RateByDestinyPriceRulesData();
				$idArrayToDelete = Tools::getValue($this->table.'Box');
				// die(var_dump($idArrayToDelete));
				foreach ($idArrayToDelete as $key => $idToDelete){
					// echo "entra<br/>";
					// die(var_dump($CatalogByDestinyPriceRulesData->id));
					$RateByDestinyPriceRulesData->id = $idToDelete;
					$result = $result && $RateByDestinyPriceRulesData->delete();
				}
				if (!$result){
					// die("aqui entra con errores");
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t delete the current object(s)');
				}

			}
			
			
			
			
			if ($result){ // funcionamiento correcto
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
			}

		}
		
		
		public function setMedia() {
			$pricerules = new Pricerules();
			$path = $pricerules->getModuleURI();
			// die($path.'js/AdminCatalogByDestinyConfigPriceRulesController.js');
			$this->context->controller->addJS($path.'js/AdminCatalogByDestinyConfigPriceRulesController.js');			
			parent::setMedia();

		}

		/** Si city no es nulo, se asigna solo a city.
		 * Sino si state no es nulo se asigna a todo el estado o provincia.
		 * Sino si country no es nulo se asigna a todo el país
		 */
		private function SetRateFor($rate, $id_country, $id_state, $id_city){
			//die("categoria :".$category." id de pais : " .$id_country. " id de estado : ".$id_state. " id de ciudad: ".$id_city);
			$correctOperation = true;
			if ($rate == null) return;
			$RateByDestinyPriceRulesData = new RateByDestinyPriceRulesData();
			if ($id_city != null){
				$RateByDestinyPriceRulesCollection = new Collection('RateByDestinyPriceRulesData');
				$RateByDestinyPriceRulesCollection->where('ID_CITY', '=', $id_city, 'where');
				$RateByDestinyPriceRulesCollection->where('ID_RATE', '=', $rate, 'where');
				$deflatedRateByDestinyCollection = $RateByDestinyPriceRulesCollection->getAll();
				$objectCounter = count($deflatedRateByDestinyCollection);
				
				// file_put_contents (  'pricerules.txt', $deflatedCatalogByDestinyCollection);
//die(var_dump($deflatedCatalogByDestinyCollection));
				if ($objectCounter == 1) {
					// EN REALIDAD ESTE TROZO NO HARIA FALTA POR NO HACERSE NADA MAS QUE GUARDARSE LO YA GUARDADO
					$RateByDestinyPriceRulesData->ID_PK = $deflatedRateByDestinyCollection[0]->id;
					$RateByDestinyPriceRulesData->id = $deflatedRateByDestinyCollection[0]->id;
					$RateByDestinyPriceRulesData->ID_CITY = $id_city;
					$RateByDestinyPriceRulesData->ID_RATE = $rate;
					$correctOperation = $correctOperation && $RateByDestinyPriceRulesData->update();

				}
				elseif ($objectCounter == 0){
					$RateByDestinyPriceRulesData->ID_CITY = $id_city;
					$RateByDestinyPriceRulesData->ID_RATE = $rate;
					//die("entra x aqui");
					$correctOperation = $correctOperation && $RateByDestinyPriceRulesData->add();				
				}
				else {
					die("El resultado de zonas por destino es inconsistente. Debe depurarse el resultado de la aplicación. If ciudad");
				}
			}
			elseif ($id_state != null){
				$cityCollection = new Collection('CitiesPriceRulesData');
				$cityCollection->where('ID_STATE', '=', $id_state, 'where');
				$cityCollection->where('ID_COUNTRY', '=', $id_country, 'where');
				$inflatedCityCollection = $cityCollection->getAll();
				foreach ($inflatedCityCollection as $clave => $filaCiudad){
				
					$id_city = $filaCiudad->ID_CITY;
					
					$RateByDestinyCollection = new Collection('RateByDestinyPriceRulesData');
					$RateByDestinyCollection->where('ID_CITY', '=', $id_city, 'where');
					$RateByDestinyCollection->where('ID_RATE', '=', $rate, 'where');
					$deflatedRateByDestinyCollection = $RateByDestinyCollection->getAll();
					$objectCounter = count($deflatedRateByDestinyCollection);
					if ($objectCounter == 1) {
						// EN REALIDAD ESTE TROZO NO HARIA FALTA POR NO HACERSE NADA MAS QUE GUARDARSE LO YA GUARDADO
						$RateByDestinyPriceRulesData->ID_PK = $deflatedRateByDestinyCollection[0]->id;
						$RateByDestinyPriceRulesData->id = $deflatedRateByDestinyCollection[0]->id;
						$RateByDestinyPriceRulesData->ID_CITY = $id_city;
						$RateByDestinyPriceRulesData->ID_RATE = $rate;
						$correctOperation = $correctOperation && $RateByDestinyPriceRulesData->update();
					}
					elseif ($objectCounter == 0){
						$RateByDestinyPriceRulesData->ID_CITY = $id_city;
						$RateByDestinyPriceRulesData->ID_RATE = $rate;
						$correctOperation = $correctOperation && $RateByDestinyPriceRulesData->add();				
					}
					else {
						die("El resultado de tarifas por destino es inconsistente. Debe depurarse el resultado de la aplicación. If state");
					}
					if (!$correctOperation) break;
				}
			}
			elseif ($id_country != null){
				$cityCollection = new Collection('CitiesPriceRulesData');
				// $cityCollection->where('id_state', '=', $id_state, 'where');
				$cityCollection->where('ID_COUNTRY', '=', $id_country, 'where');
				$inflatedCityCollection = $cityCollection->getAll();
				foreach ($inflatedCityCollection as $clave => $filaCiudad){
								
					$id_city = $filaCiudad->ID_CITY;
					
					$RateByDestinyCollection = new Collection('RateByDestinyPriceRulesData');
					$RateByDestinyCollection->where('ID_CITY', '=', $id_city, 'where');
					$RateByDestinyCollection->where('ID_RATE', '=', $rate, 'where');
					$deflatedRateByDestinyCollection = $RateByDestinyCollection->getAll();
					$objectCounter = count($deflatedRateByDestinyCollection);
					//die("object counter vale: ".$objectCounter. "y su vardump vale: ".var_dump($objectCounter));
					if ($objectCounter == 1) {
					
						//die(var_dump($deflatedCatalogByDestinyCollection[0]));
					
						// EN REALIDAD ESTE TROZO NO HARIA FALTA POR NO HACERSE NADA MAS QUE GUARDARSE LO YA GUARDADO
						$RateByDestinyPriceRulesData->ID_PK = $deflatedRateByDestinyCollection[0]->id;
						$RateByDestinyPriceRulesData->id = $deflatedRateByDestinyCollection[0]->id;
						$RateByDestinyPriceRulesData->ID_CITY = $id_city;
						$RateByDestinyPriceRulesData->ID_RATE = $rate;
						$correctOperation = $correctOperation && $RateByDestinyPriceRulesData->update();
					}
					elseif ($objectCounter == 0){
						$RateByDestinyPriceRulesData->ID_CITY = $id_city;
						$RateByDestinyPriceRulesData->ID_RATE = $rate;
						$correctOperation = $correctOperation && $RateByDestinyPriceRulesData->add();				
					}
					else {
						die("El resultado de tarifas por destino es inconsistente. Debe depurarse el resultado de la aplicación. If country");
					}
					if (!$correctOperation) break;
				}			
			}
				
			if (!$correctOperation) 
				$this->errors[] = Tools::displayError('An error has occurred: Can\'t save or update the current object(s)');
			//else
				//Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
			return $correctOperation;
		}		
		
	}
?>