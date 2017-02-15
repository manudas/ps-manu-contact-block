

$(document).ready(function() { 
	// $('#container-callme-please').click(function() {
	$('#one-callme-please').click(function() {
		$('#container-callme-please').css('height', 'auto');
		var contentHeight = $('#two-callme-please').children('div').outerHeight();
		//alert(contentHeight);
		$('#two-callme-please').css('max-height', contentHeight + 'px');
		$('#two-callme-please').css('overflow', 'visible');
		$('#close_buttom-callme-please').css('visibility', 'visible');
	});

	$('#close_buttom-callme-please').click(function(e){
		close_call_form(e);
	});
	$('#bt_enviar-callme-please').click(function(e){
		sendCallRequest(e);
	});
});



function close_call_form(event){
	//$('#container-callme-please').css('height', '37px');
	$('#two-callme-please').css('max-height', '0');
	$('#two-callme-please').css('overflow', 'hidden');
	$('#close_buttom-callme-please').css('visibility', 'hidden');
	event.stopPropagation();
}

function sendCallRequest(event){
    if(($("#customer_name").val()=="")||($("#customer_phone").val()=="")||($("#customer_country").val()=="")){
    	showMessageDialog("<h2>Información incompleta - Incomplete data</h2>Por favor, introduzca todos los datos antes de enviar la solicitud<br/><hr/>Please, enter all the fields. They are required to send the inquiry","Incompleto - Incomplete");
        return;
    }
    
    // var patron_telefono = new RegExp("/^(\s*(((\+\s*\d+)|00\s*\d+)|((\(\s*\+\s*\d+\s*\))|(\(\s*00\s*\d+\s*\)))|((\+\s*\(\s*\d+\s*\))|\s*00\(\\s*d+\s*\)))\s*){0,1}(((\d+\s*-\s*\d+)|(\(\d+\s*-\s*\d+\))|(\d+)|(\(\d+\)))\s*)+$/g");
    var patron_telefono = new RegExp("^(\\s*(((\\+\\s*\\d+)|00\\s*\\d+)|((\\(\\s*\\+\\s*\\d+\\s*\\))|(\\(\\s*00\\s*\\d+\\s*\\)))|((\\+\\s*\\(\\s*\\d+\\s*\\))|\\s*00\\(\\s*\\d+\\s*\\)))\\s*){0,1}(((\\d+\\s*-\\s*\\d+)|(\\(\\d+\\s*-\\s*\\d+\\))|(\\d+)|(\\(\\d+\\)))\\s*)+$","g");
    var telefono = $("#customer_phone").val();
    var es_telefono_valido = patron_telefono.test(telefono);
    if (!es_telefono_valido) {
    	showMessageDialog("<h2>Formato de teléfono incorrecto - Phone in wrong format</h2>No se reconoce el formato de su teléfono. Solo se admite el formato +XXYYYYYYY o 00XXYYYYYYY, siendo XX el código de país e YYYYYYY el número de teléfono<br/><hr/>The format of your phone is not recognized. We only allow the format +XXYYYYYYY or 00XXYYYYYYY, with XX being the country code and YYYYYYY the phone number","Formato incorrecto - Wrong format");
    	return;
    }
    telefono = encodeURIComponent(telefono); 

    var data="ajax=true&action=addRequest&customer_name="+$("#customer_name").val()+"&customer_phone="+telefono+"&customer_country="+$("#customer_country").val();
    $.ajax({
        data:		data,
        url:		callmeplease_path,
        type:		'post',
        // dataType:	'json',
        success:	function(response){
				//alert(response);
                if (response == 1)
                {
                	showMessageDialog("<h2>Petición enviada - Request sent</h2>Un operador la llamará en breve<br/><hr/>Our customer service will call you as soon as possible","Petición enviada");
                    // peticion_enviada = true;
                }
                else if (response == 0) { /* Error del sistema */
                	showMessageDialog("<h2>Error en el sistema - System Error</h2>Un error ha impedido que se envíe su solicitud, por favor llame a nuestro servicio de atención al cliente, donde le atenderemos inmediatamente<br/><hr/>An error has prevented your request to be submitted. Please call our customer service where we will attend you immediately","Error");
                }
                else if (response == -1) { // llamada en cola de espera
                	showMessageDialog("<h2>En proceso - Queued</h2>Su llamada se encuentra en la cola de espera. Por favor espere, un operador le llamará lo antes posible<br/><hr/>Your call is queued. Please wait, an operator will call you as soon as possible","Error");
                }
        }
    });
    close_call_form(event);
}

function showMessageDialog(message, titulo, objButtons){
	
	titulo = titulo || "";
	objButtons = objButtons || {};
	/* hack para que salga la x en el botón de cerrado */
	if (typeof $.fn.button.noConflict == 'function') { // check if function is defined
		var bootstrapButton = $.fn.button.noConflict() // return $.fn.button to previously assigned value
		$.fn.bootstrapBtn = bootstrapButton            // give $().bootstrapBtn the Bootstrap functionality
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
	
	
	//$dialog.html("");
    var dialogZIndex = maxzindex+1;
    
	$dialog.dialog({
        autoOpen: false,
        resizable: true,
        width: dWidth,
        
        draggable: true,
        closeOnEscape: true,
        hide: { effect: "explode", duration: 1000 },
        show: { effect: "blind", duration: 800 },
        title: title,
        close: function(event, ui) {$(this).dialog("close");},
        // zIndex: dialogZIndex,
        
        height: dHeight,
        modal: true,
        buttons: buttons
    });

	
	$dialog.dialog("open");
	
	$( '.ui-front' ).css('zIndex',dialogZIndex);
	$( '.ui-dialog' ).css('zIndex',dialogZIndex);

}

$( document ).ready(function() {
	var maxZ=0;
	var allElements = $( "*" );
	allElements.each(function(){
		var currentZIndex = $(this).css('zIndex');
		if ($.isNumeric(currentZIndex)){
		    if(currentZIndex > maxZ) {
		    	maxZ = currentZIndex;
		    }
		}
	});
	maxzindex = parseInt(maxZ);
});