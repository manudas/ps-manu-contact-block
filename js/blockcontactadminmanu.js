function toggleCallStatus() {
	var a = "action=toggleCallStatus&ajax=true";

	$.ajax({
		data : a,
		url : callmeplease_path,
		type : "post",
		success : function(data) {
			// alert(a);
			// return;
			if (1 == data)
				if ($("#btChangeCallStatus").hasClass(
						"btAdminCallMePleaseGreen")) {
					// si entra por aquí: llamadas actualmente activadas
					$("#btChangeCallStatus")
							.html("desactivar llamadas");
					$("#btChangeCallStatus").removeClass(
							'btAdminCallMePleaseGreen');
					$("#btChangeCallStatus").addClass(
							'btAdminCallMePleaseRed');
				} else {
					// si entra por aquí, llamadas actualmente
					// desactivadas
					$("#btChangeCallStatus").html("activar llamadas");
					$("#btChangeCallStatus").removeClass(
							'btAdminCallMePleaseRed');
					$("#btChangeCallStatus").addClass(
							'btAdminCallMePleaseGreen');
				}
		}
	});
}

function attendInquiry(a) {

	var b = "action=requestIsBeingAttended&inquiry_id=" + a + "&ajax=true" + "&employee=" + $("#employee").val();
	$.ajax({
		data : b,
		url : callmeplease_path,
		type : "post",
		success : function(data) {
			if (1 == data)
				inquiry_is_being_attended(a);
		}
	});
}

function customerHasBeenAttended(a) {

	var b = "action=requestHasBeenAttended&inquiry_id=" + a + "&ajax=true";
	$.ajax({
		data : b,
		url : callmeplease_path,
		type : "post",
		success : function(data) {
			if (1 == data)
				remove_inquiry(a);
		}
	});
}

function remove_inquiry(a) {
	var call_line_obj = $("#inquiry_" + a);
	call_line_obj.hide(500, function() {
		this.remove();
		removeHash(a);
	});
}

function setInquiryCode(inquiry_id, code) {
	var call_line_obj = $("#inquiry_" + inquiry_id);
	call_line_obj.html(code);
}

function inquiry_is_being_attended(a) {
	var call_line_obj = $("#inquiry_" + a);
	call_line_obj.addClass("isBeingAttendedAnimationClass");
}

function playAlertSound() {
	if ("true" == sonido) {
		var audio_element = document.getElementById("callmePleaseAlarm");
		audio_element.play();
	}
}

function toggleCallList() {
	$("#callListContainer").toggle();
}

function checkCurrentInquiryStatuses() {

	if (typeof inquiryStatusesRequestFinished === 'undefined') {
		inquiryStatusesRequestFinished = true;
	}

	if (inquiryStatusesRequestFinished == false)
		return; // we avoid new petitions
	
	

	var a = new Array();
	$("[name='id_inquiry']").each(function() {
		a.push($(this).val());
	});
	// var b = JSON.stringify(a);
	if (a.length > 0) {

		var c = {
			action : "checkCurrentInquiryStatuses",
			id_inquiry_list : a,
			"ajax" : true
		};
		try {
			$.ajax({
				data : c,
				url : callmeplease_path,
				type : "post",
				beforeSend: function() {
			        // alert('checkCurrentInquiryStatuses');
			        inquiryStatusesRequestFinished = false;
			    },
				dataType : "json"
			}).done(function(data) {
				showCurrentStatus(data);
			}).always(function(jqXHR, textStatus) {
				// alert("always called");
				inquiryStatusesRequestFinished = true;
			});
		}
		catch (e){
			inquiryStatusesRequestFinished = true;
		}
	}
}

function geNewHash(inquiry_object) {
	return inquiry_object.hash;
}
function removeHash(id_inquiry) {
	delete inquiry_hash_array[id_inquiry];
}
function geCurrentHash(id_inquiry) {
	return inquiry_hash_array[id_inquiry];
}
function setHash(id_inquiry, hash) {
	inquiry_hash_array[id_inquiry] = hash;
}

function showCurrentStatus(xhrResponse) {

	if (1 == xhrResponse.flag) {
		var modifiedStatusCalls = xhrResponse.modifiedStatusCall;

		for (var i = 0; i < modifiedStatusCalls.length; i++) {
			var current_call = modifiedStatusCalls[i];
			var status = current_call.inquiry_status;
			var inquiry_ID = current_call.inquiry_ID;
			if (status == 'a') { // already attended
				remove_inquiry(inquiry_ID);
			} else { // la solicitud se ha actualizado de otra manera
						// distinta, adjuntamos su nuevo código html
				var old_hash = geCurrentHash(inquiry_ID);
				var new_hash = geNewHash(current_call);
				if (old_hash != new_hash) {
					setInquiryCode(inquiry_ID, current_call.inquiry_admin_code);
					setHash(inquiry_ID, new_hash);
				}
			}
		}
	}
}

function checkNewInquiries() {

	if (typeof newInquiriesRequestFinished === 'undefined') {
		newInquiriesRequestFinished = true;
	}

	if (newInquiriesRequestFinished == false)
		return; // we avoid new petitions
	

	// ajax=true&action=getInquiries
	var data = "action=getInquiries&ajax=true";
	
	try { 
		$.ajax({
			data : data,
			url : callmeplease_path,
			type : "post",
			beforeSend: function() {
				newInquiriesRequestFinished = false;
		    },
			dataType : "json",
		}).done(function(data) {
			if (data.flag == 1) {
				addInquiries(data.callList);
			}
		}).always(function(jqXHR, textStatus) {
			// alert("always called");
			newInquiriesRequestFinished = true;
		});
	}
	catch (e){
		newInquiriesRequestFinished = true;
	}
}

function addInquiries(callList) {
	var newCallsAdded = false;
	for (i = 0; i < callList.length; i++) {
		var elementID = callList[i].inquiry_ID;
		var callCode = getNewCallCode(callList[i]);
		var hash = geNewHash(callList[i]);

		if ($("#inquiry_" + elementID).length == 0) {
			// Doesn`t exists in DOM, let`s add it
			// $("#backOfficeCallmeWindow").append( callCode );
			$("#callListContainer").append(callCode);
			newCallsAdded = true;
			setHash(elementID, hash);
		} else { // ya existe, ¿pero cambió de estado ? Comprobamos su hash
			var old_hash = geCurrentHash(elementID);
			if (hash != old_hash) {
				$("#inquiry_" + elementID).replaceWith(callCode);
				setHash(elementID, hash);
			}
		}
	}
	if (newCallsAdded == true) {
		playAlertSound();
	}
}

function getNewCallCode(call) {
	if (typeof call !== 'undefined')
		return call.inquiry_admin_code;
	else
		return null;
}

function toggleSound() {
	// var data = "action=toggleSound&ajax=true";
	var b = "action=toggleSound&ajax=true";
	$.ajax({
		data : b,
		url : callmeplease_path,
		type : "post",
		success : function(response) {
			if (response == 1) {
				if (sonido == true)
					sonido = false;
				else
					sonido = true;

				if (sonido == true) {
					$("#btSonido").html("desactivar sonido");

					$("#btSonido").removeClass('btAdminCallMePleaseGreen');
					$("#btSonido").addClass('btAdminCallMePleaseRed');
				} else {
					$("#btSonido").html("activar sonido");

					$("#btSonido").removeClass('btAdminCallMePleaseRed');
					$("#btSonido").addClass('btAdminCallMePleaseGreen');
				}

			}
		}
	});
}

function showCommentDialog(current_inquiry_id) {

	/* hack para que salga la x en el botón de cerrado */
	if (typeof $.fn.button.noConflict == 'function') { // check if function is
														// defined
		var bootstrapButton = $.fn.button.noConflict() // return $.fn.button to
														// previously assigned
														// value
		$.fn.bootstrapBtn = bootstrapButton // give $().bootstrapBtn the
											// Bootstrap functionality
	}
	/* fin hack */
	var selectedCheckedBoxFieldSetString = '#modifierCommentOptionsCheckBoxArray_'
			+ current_inquiry_id + ' input[type=checkbox]:checked';
	var selectedCheckedBoxFieldSetString_OBJ = $(selectedCheckedBoxFieldSetString);

	var atLeastOneIsChecked = selectedCheckedBoxFieldSetString_OBJ.length > 0;

	if (!atLeastOneIsChecked) { // no se selecciono ningún checkbox
		var title = "Error, no option selected";
		var $dialog = $('<div id="commentDialogError"><h2>¡ERROR!</h2> <br/>No se selecciono ninguna opción</div>');
		var buttons = {};
	} else { // hay al menos un checkbox seleccionado
		var title = "Managing the comment";
		var $dialog = $('#commentDialog');

		// Si nunca se ha ejecutado el método entramos por aquí
		if (typeof last_inquiry_id === 'undefined') {
			last_inquiry_id = current_inquiry_id;
		} else {
			if (current_inquiry_id != last_inquiry_id) {
				// si la anterior ejecución del método se corresponde con otra
				// petición distinta borramos el texto del comentario anterior
				$('#commentTextArea').html("");
				last_inquiry_id = current_inquiry_id;
			}
		}

		var buttons = {
			"Submit" : function() {
				sendComment(current_inquiry_id);
				$(this).dialog("close");
			}
		};

	}

	// dialog size: 65% of the window size
	var wWidth = $(window).width();
	var dWidth = wWidth * 0.65;

	var wHeight = $(window).height();
	var dHeight = wHeight * 0.65;

	// $dialog.html("");
	var dialogZIndex = maxzindex + 1;

	$dialog.dialog({
		autoOpen : false,
		resizable : true,
		width : dWidth,

		draggable : true,
		closeOnEscape : true,
		hide : {
			effect : "explode",
			duration : 1000
		},
		show : {
			effect : "blind",
			duration : 800
		},
		title : title,
		close : function(event, ui) {
			$(this).dialog("close");
		},
		// zIndex: dialogZIndex,

		height : dHeight,
		modal : true,
		buttons : buttons
	});

	$dialog.dialog("open");

	$('.ui-front').css('zIndex', dialogZIndex);
	$('.ui-dialog').css('zIndex', dialogZIndex);

	// $dialog.dialog( "moveToTop" );
	// uso: .dialog(opcion, opcion a cambiar, valor de la opcion a cambiar)
	// $("#commentDialog").dialog("option", "title",
	// "Loading....").dialog("open");
	// $("span.ui-dialog-title").text('title here');
}

function showMessageDialog(message, titulo, objButtons) {

	titulo = titulo || "";
	objButtons = objButtons || {};

	/* hack para que salga la x en el botón de cerrado */
	if (typeof $.fn.button.noConflict == 'function') { // check if function is
														// defined
		var bootstrapButton = $.fn.button.noConflict() // return $.fn.button to
														// previously assigned
														// value
		$.fn.bootstrapBtn = bootstrapButton // give $().bootstrapBtn the
											// Bootstrap functionality
	}
	/* fin hack */

	var title = titulo;
	var $dialog = $('<div></div>');
	var buttons = objButtons;
	$dialog.html(message);

	// dialog size: 65% of the window size
	var wWidth = $(window).width();
	var dWidth = wWidth * 0.65;

	var wHeight = $(window).height();
	var dHeight = wHeight * 0.65;

	// $dialog.html("");
	var dialogZIndex = maxzindex + 1;

	$dialog.dialog({
		autoOpen : false,
		resizable : true,
		width : dWidth,

		draggable : true,
		closeOnEscape : true,
		hide : {
			effect : "explode",
			duration : 1000
		},
		show : {
			effect : "blind",
			duration : 800
		},
		title : title,
		close : function(event, ui) {
			$(this).dialog("close");
		},
		// zIndex: dialogZIndex,

		height : dHeight,
		modal : true,
		buttons : buttons
	});

	$dialog.dialog("open");

	$('.ui-front').css('zIndex', dialogZIndex);
	$('.ui-dialog').css('zIndex', dialogZIndex);

}

function sendComment(current_inquiry_id) {
	// ajax=true&action=getInquiries
	var text = $("#commentTextArea").val();
	var metodo = "";
	var insertionMethod = {};
	if ($("#ALL_checkbox_" + current_inquiry_id + ":checked").length > 0) {
		insertionMethod['ip'] = true;
		insertionMethod['session_id'] = true;
		insertionMethod['customer_phone'] = true;
		metodo = "ip, sesión, número de teléfono";
	} else {
		// var insertionMethod = "";
		if ($("#IP_checkbox_" + current_inquiry_id + ":checked").length > 0) {
			insertionMethod['ip'] = true;
			metodo = "ip";
		}
		if ($("#SESSION_checkbox_" + current_inquiry_id + ":checked").length > 0) {
			insertionMethod['session_id'] = true;
			if (metodo.length == 0) {
				metodo = "sesión";
			} else {
				metodo += ", sesión";
			}
		}
		if ($("#PHONE_checkbox_" + current_inquiry_id + ":checked").length > 0) {
			insertionMethod['customer_phone'] = true;
			if (metodo.length == 0) {
				metodo = "número de teléfono";
			} else {
				metodo += ", número de teléfono";
			}
		}
	}
	var insertionMethod;
	var data = {
		action : "sendComment",
		comment : text,
		insertionMethod : insertionMethod,
		id_inquiry : current_inquiry_id,
		ajax : true
	};
	$.ajax({
				data : data,
				url : callmeplease_path,
				type : "post",
				dataType : "json",
				success : function(response) {
					if (response.flag == 1) {
						showMessageDialog(
								"<h2>Insercción correcta</h2>Se ha insertado el comentario, puede insertar uno extra o buscar este comentario cuando lo necesite. El sistema le indicará que tiene un comentario por estos métodos: "
										+ metodo, "Insercción correcta");
					} else if (response.flag == 0) {
						showMessageDialog(
								"<h2>Error de insercción</h2>Un error impidió insertar el comentario correctamente. El sistema intentó insertar el comentario por estos métodos: "
										+ metodo, "Error de insercción");
					}

				}
			});
}

function submitSearchForComment(id_inquiry) {
	if ($('#' + "ALL_checkbox_" + id_inquiry).is(":checked")) {
		$("#IP_input_comment_" + id_inquiry).prop('disabled', false);
		$("#SESSION_input_comment_" + id_inquiry).prop('disabled', false);
		$("#PHONE_input_comment_" + id_inquiry).prop('disabled', false);
	} else {
		if ($('#' + "IP_checkbox_" + id_inquiry).is(":checked")) {
			$("#IP_input_comment_" + id_inquiry).prop('disabled', false);
		} else {
			$("#IP_input_comment_" + id_inquiry).prop('disabled', true);
		}
		if ($('#' + "SESSION_checkbox_" + id_inquiry).is(":checked")) {
			$("#SESSION_input_comment_" + id_inquiry).prop('disabled', false);
		} else {
			$("#SESSION_input_comment_" + id_inquiry).prop('disabled', true);
		}
		if ($('#' + "PHONE_checkbox_" + id_inquiry).is(":checked")) {
			$("#PHONE_input_comment_" + id_inquiry).prop('disabled', false);
		} else {
			$("#PHONE_input_comment_" + id_inquiry).prop('disabled', true);
		}
	}
	return true;
}

function submitSearchForCustomer(form, id_inquiry) {
	if (string_one_contains_string_two($(form.submited).attr('id'), "ip")) {
		$("#IP_input_" + id_inquiry).prop('disabled', false);
	} else {
		$("#IP_input_" + id_inquiry).prop('disabled', true);
	}
	if (string_one_contains_string_two($(form.submited).attr('id'), "session")) {
		$("#SESSION_input_" + id_inquiry).prop('disabled', false);
	} else {
		$("#SESSION_input_" + id_inquiry).prop('disabled', true);
	}
	if (string_one_contains_string_two($(form.submited).attr('id'), "phone")) {
		$("#PHONE_input_" + id_inquiry).prop('disabled', false);
	} else {
		$("#PHONE_input_" + id_inquiry).prop('disabled', true);
	}
	return true;
}

function registerButtonInSearchForCustomerFrom(form, button) {
	form.submited = button;
}

function string_one_contains_string_two(string1, string2) {
	return string1.indexOf(string2) !== -1;
}

/*
 * function hidePanels(panel){
 * 
 * var id = panel.id;
 * 
 * var panel_list = $(panel).find("."+id+":visible");
 * 
 * var reverse_list = panel_list.reverse(); // cerramos primero el hijo y luego
 * el padre reverse_list.each(function(){ this.hide(50, function() { //
 * this.remove(); }); })
 *  }
 */

function send_quick_comment_enter_event_listener(e, id_new_fast_comment) {
	//$("#comentario_rapido_"+id_new_fast_comment).keypress(function(e) {
		var key = e.which;
		if (key == 13) // the enter key code
		{
			// Si nunca se ha ejecutado el método entramos por aquí
			if (typeof last_inquiry_id === 'undefined') {
				last_inquiry_id = id_new_fast_comment;
			} else {
				if (id_new_fast_comment != last_inquiry_id) {
					// si la anterior ejecución del método se corresponde con
					// otra petición distinta lo anotamos para comentarios no rápidos
					
					last_inquiry_id = id_new_fast_comment;
				}
			}
			$('#commentTextArea').html($("#comentario_rapido_"+id_new_fast_comment).val());
			sendComment(id_new_fast_comment);
			return false;
		}
	//});
}
/*
function set_comentario_rapido_events(){
	$('[name="comentario_rapido[]"]').each(function(){
		var input_id_string = $(this).attr('id');
		var comment_id = input_id_string.substr("comentario_rapido_".length);
		send_quick_comment_enter_event_listener(comment_id);
	});
}
*/
function getMaxZ_index(){
	var maxZ = 0;
	var allElements = $("*");
	allElements.each(function() {
		var currentZIndex = $(this).css('zIndex');
		if ($.isNumeric(currentZIndex)) {
			if (currentZIndex > maxZ) {
				maxZ = currentZIndex;
			}
		}
	});
	maxzindex = parseInt(maxZ);
}


// vamos a modificar esto de forma que solo sea necesario una funcion a la que
// añadiremos mediante json el campo status
// que será n=new, o=older, b= being attended, a=already attended (borrar).
// Se añadirá un nuevo campo en el json para el caso en el que la llamada sea
// being attended y already attended que
// contendrá el códig de la línea que se mostrará en el back office

$(document).ready(function() {
	getMaxZ_index();
	//set_comentario_rapido_events();
});

setInterval(checkNewInquiries, 1e4); // 1e4 = 1*10^4

setInterval(checkCurrentInquiryStatuses, 1e4); // 1e4 = 1*10^4

/*
 * var oldJQueryEventTrigger = jQuery.event.trigger; jQuery.event.trigger =
 * function( event, data, elem, onlyHandlers ) { if ((typeof elem !==
 * 'undefined')&&(elem.id == 'PHONE_input_1')){ //if ((typeof elem !==
 * 'undefined')){ //if (elem.id != "") alert (elem.id); console.log( event,
 * data, elem, onlyHandlers );PHONE_input_1 } //} oldJQueryEventTrigger( event,
 * data, elem, onlyHandlers ); }
 */