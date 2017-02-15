<?php

	class blockcontactmanuFrontCallmePleaseModuleFrontController extends ModuleFrontController{
		
		public function displayAjax() {
			// die("holiiiiiii");
			$action = Tools::getValue('action');
			/*
			header('Access-Control-Allow-Origin: *');
			header('HTTP/1.0 403 Not Found', true, 403);
			die(var_dump($_SERVER['HTTP_ORIGIN']));
			*/
			$blockcontactmanu = Module::getInstanceByName ( 'blockcontactmanu' );
			if (!$blockcontactmanu -> isCrossOriginAllowed() ){
				// NO ES AUTORIZADO
				$_origin = strtolower($_SERVER['HTTP_ORIGIN']);
				$_current_host = strtolower($_SERVER['HTTP_HOST']);
				
				// echo "Comentario de debug: no es autorizado. origin es: $_origin";
				
				
				if (empty($_origin)) {
					// SOME BROWSERS DOESN'T ADD THIS HEADER ON SAME DOMAIN REQUEST: do nothing. Same origin doesn't require Access-Control-Allow-Origin
				}
				else if(strpos( $_origin, $_current_host ) === false){ // NO ES EL MISMO HOST, Y ADEM�S NO ES AUTORIZADO
					header('Access-Control-Allow-Origin: *');
					header('HTTP/1.0 404 Not Found', true, 404);
					
					// die(var_export($_SERVER, true));
					echo "404 not found" /*. origin-pajar: $_origin?? aguja-currenthost:  $_current_host*/;
					exit;
				}
			}
			else {
				// echo "Comentario de debug: si es autorizado";
				// exit;
			}
			
			// SI ES AUTORIZADO SALE POR AQUI, POR LO TANTO AUTORIZAMOS
			header('Access-Control-Allow-Origin: *');
			
			switch ($action){
				case 'addRequest':
					session_start();
					$customer_name = Tools::getValue('customer_name');
					$customer_phone = Tools::getValue('customer_phone');
					// die($customer_phone);
					$customer_country = Tools::getValue('customer_country');
					// $blockcontactmanu = Module::getInstanceByName('blockcontactmanu');
					// $callmeplease = new callmeplease();
					try {
						$result = $blockcontactmanu->insertNewCallInquiry($customer_name, $customer_phone, $customer_country);
					}
					catch (Exception $e) { // posible error: el cliente sobrepasa el n�mero de caracteres permitidos para un campo
						echo 0;
						break;
					}
					// echo var_export($result, true);
					
					if ($result == true) echo 1;
					else {
						$already_exists = $blockcontactmanu -> unatendedCallInquiryExists($customer_name, $customer_phone, $customer_country);
						if ($already_exists == true){ // ya existe, enviamos código de llamada a la espera
							echo -1; // llamada a la espera
						}
						else {
							echo 0; // error del sistema
						}
					}
					
					break;
				case 'requestHasBeenAttended' :
					$inquiry_id = Tools::getValue ( 'inquiry_id' );
					
					// $callmeplease = new callmeplease();
					$result = $blockcontactmanu->callInquiryHasBeenAttended ( $inquiry_id );
					if ($result)
						echo 1;
					break;
					
				case 'requestIsBeingAttended' :
					$inquiry_id = Tools::getValue ( 'inquiry_id' );
					$employee = Tools::getValue ( 'employee' );
					// $blockcontactmanu = Module::getInstanceByName ( 'blockcontactmanu' );
					// $callmeplease = new callmeplease();
					$result = $blockcontactmanu->callInquiryIsBeingAttended ( $inquiry_id, $employee );
					if ($result)
						echo 1;
					break;
					
				case 'getInquiries' :
					// $blockcontactmanu = Module::getInstanceByName ( 'blockcontactmanu' );
					// $callmeplease = new callmeplease();
					$partial_result = $blockcontactmanu->getPendingInquiryCalls ();
					if (! empty ( $partial_result )) {
						$result->flag = 1;
						$result->callList = $partial_result;
					} else {
						$result->flag = 0;
					}
					echo json_encode ( $result );
					break;
					
				case 'toggleSound' :
					// session_start();
					
					$cookie = new Cookie ( "callmeplease" );
					if ((isset ( $cookie->sonido_llamadas )) && ($cookie->sonido_llamadas == 'true')) {
						$cookie->sonido_llamadas = 'false';
					} else
						$cookie->sonido_llamadas = 'true';
								
					// $cookie->sonido_llamadas
					echo 1;
					break;
					
				case 'toggleCallStatus' :
					
					$value = Configuration::get ( 'BLOCKCONTACT_CALLMEPLEASE_ACTIVATED' );
					// echo $value;
					
					if ($value == '1') {
						Configuration::updateValue ( 'BLOCKCONTACT_CALLMEPLEASE_ACTIVATED', '0' );
					} else {
						Configuration::updateValue ( 'BLOCKCONTACT_CALLMEPLEASE_ACTIVATED', '1' );
					}
					echo 1;
					break;
					
				case 'checkCurrentInquiryStatuses' :					
					// $codified_id_inquiry_list = Tools::getValue ( 'id_inquiry_list' );
					$id_inquiry_list = Tools::getValue ( 'id_inquiry_list' );
					
					//die("id_inquiry_status_list es: ".$id_inquiry_list);
						
					// die("id_inquiry_status_list es: ".json_encode($id_inquiry_list));
					
					// $id_inquiry_list = json_decode ( $codified_id_inquiry_list );
					// $blockcontactmanu = Module::getInstanceByName ( 'blockcontactmanu' );
					// $callmeplease = new callmeplease();
					$modifiedStatusInquiry = $blockcontactmanu->checkInquiryStatuses ( $id_inquiry_list );
					
					if (! empty ( $modifiedStatusInquiry )) {
						$result->flag = 1;
						$result->modifiedStatusCall = $modifiedStatusInquiry;
					} else {
						$result->flag = 0;
					}
					
					echo json_encode ( $result );
					
					break;
					
				case 'sendComment' :
					// $blockcontactmanu = Module::getInstanceByName ( 'blockcontactmanu' );
					$insertionMethodArr = Tools::getValue ( 'insertionMethod' );
					$id_inquiry = Tools::getValue ( 'id_inquiry' );
					$comment = Tools::getValue ( 'comment' );
					
					$ok = $blockcontactmanu->insertComment ( $comment, $insertionMethodArr, $id_inquiry );
					if ($ok) {
						$result->flag = 1;
					} else {
						$result->flag = 0;
					}
					echo json_encode ( $result );
					
					break;
					
				case 'checkCallmepleaseActivation':
					echo Configuration::get('BLOCKCONTACT_CALLMEPLEASE_ACTIVATED');
					break;
			}
		}
		
	}
?>