{function name=comentarios_control}
	<div class='controlComentario elementoSombreado'>
		<form target="_blank" method="get" action="{$callmepleasecomments_path}" onSubmit="submitSearchForComment({$solicitud_llamada->inquiry_ID})">
			<label class="labelComment labelCommentButtons">Comentarios</label>
			{if $comentarios}
				<div class="twoButtonsOptionComments">
					<button type='submit' >{l s='Buscar'} </button>
					<button type='button' onclick="showCommentDialog({$solicitud_llamada->inquiry_ID});" >{l s='Add'} </button>
				</div>	
				<div class="modifiersOptionsComments modifiersWithTwoButtons" id="modifierCommentOptionsCheckBoxArray_{$solicitud_llamada->inquiry_ID}">
			{else}
				<div class="oneButtonOptionComment">
					<button type='button' onclick="showCommentDialog({$solicitud_llamada->inquiry_ID});" >{l s='Add'} </button>
				</div>	
				<div class="modifiersOptionsComments modifiersWithOneButton" id="modifierCommentOptionsCheckBoxArray_{$solicitud_llamada->inquiry_ID}">
			{/if}
					<input name="submitFilter" value="1" type="hidden" />
					<input name="callme_please_commentsFilter_a!ip" id="IP_input_comment_{$solicitud_llamada->inquiry_ID}" type="hidden" value="{$inquiry_data->ip}" disabled/>
					<input name="callme_please_commentsFilter_a!session_id" id="SESSION_input_comment_{$solicitud_llamada->inquiry_ID}" type="hidden" value="{$inquiry_data->session_id}" disabled/>
					<input name="callme_please_commentsFilter_a!customer_phone" id="PHONE_input_comment_{$solicitud_llamada->inquiry_ID}" type="hidden" value="{$solicitud_llamada->customer_phone}" disabled/>
					<label class="labelComment">{l s='IP'}&nbsp</label><input class="modifierCommentOptions" type='checkbox' id="IP_checkbox_{$solicitud_llamada->inquiry_ID}"/>&nbsp;
					<label class="labelComment">{l s='Session'}&nbsp</label><input class="modifierCommentOptions" type='checkbox' id="SESSION_checkbox_{$solicitud_llamada->inquiry_ID}" checked/>&nbsp;
					<label class="labelComment">{l s='Phone'}&nbsp</label><input class="modifierCommentOptions" type='checkbox' id="PHONE_checkbox_{$solicitud_llamada->inquiry_ID}"/>&nbsp;
					<label class="labelComment">{l s='Todos'}&nbsp</label><input class="modifierCommentOptions" type='checkbox' id="ALL_checkbox_{$solicitud_llamada->inquiry_ID}"/>
				</div>
		</form>						
	</div>
{/function}

{function name=comentario_rapido_nuevo id=""}
	<div class='commentContainer'>	
		<input name='comentario_rapido[]' placeholder="{l s='Nuevo, intro para aceptar'}" class='adminInputOriginURL elementoSombreado' id="comentario_rapido_{$id}" onkeydown="send_quick_comment_enter_event_listener(event, {$id})"/>
		{comentarios_control}
	</div>
{/function}

{function name=comentariof key=0}
{*($key lte $comentarios->comment_collection|@count)|var_dump*}
	{if ($comentarios->comment_collection|is_array) AND ($comentarios->comment_collection|@count gt 0) AND ($key lt $comentarios->comment_collection|@count)} {* gt == greater than ; lte == less than equal *}		
		{strip}
		<div class='commentContainer'>
			<input name='comentario[]' value=
				'{l s='Comentarios por: '}
				{if $comentarios->restrictions_found[$key]['customer_phone']}
					{l s='Phone'}
					{if $comentarios->restrictions_found[$key]['session_id'] OR $comentarios->restrictions_found['ip']}
						,&nbsp;
					{/if}
				{/if}
				{if $comentarios->restrictions_found[$key]['session_id']}		
					{l s='session'}
					{if $comentarios->restrictions_found[$key]['ip']}
						,&nbsp;
					{/if}
				{/if}
				{if $comentarios->restrictions_found[$key]['ip']}
					{l s='IP'}
				{/if}
				{*$comentarios->comment_collection[$key]->comment|var_dump*}
				.&nbsp;{l s='Comentario'}: {$comentarios->comment_collection[$key]->comment}'
					class='adminInputOriginURL elementoSombreado' />
			{comentariof key = $key+1}
		</div>
		{/strip}
	{else}
		{if ($key lt $comentarios->comment_collection|@count) neq true}
			{comentario_rapido_nuevo id={$solicitud_llamada->inquiry_ID}}
		{/if}
	{/if}
{/function}

{function name=sin_comentarios}
	<div class='commentContainer'>					
		<input name='comentario[]' value=
			'{l s='No tiene comentarios'}'
			class='adminInputOriginURL elementoSombreado' />
		{comentario_rapido_nuevo id={$solicitud_llamada->inquiry_ID}}
	</div>
{/function}

{function name=comentarios_function }
	{if $comentarios}
		{comentariof}
	{else}
		{sin_comentarios}
	{/if}
{/function}



<div id='inquiry_{$solicitud_llamada->inquiry_ID}' class="contenedorSolicitud 
		{if $solicitud_llamada->inquiry_status == 'p'}
			isBeingAttendedAnimationClass
		{/if}
	"> 
	<input type='hidden' name='id_inquiry' value='{$solicitud_llamada->inquiry_ID}' />
	<input name='customer_name[]' value='{$solicitud_llamada->customer_name}' class='adminInputInfoCallMePlease' />
	<input name='customer_phone[]' value='{$solicitud_llamada->customer_phone}' class='adminInputInfoCallMePlease' />
	<input name='customer_country[]' value='{$solicitud_llamada->customer_country}' class='adminInputInfoCallMePlease' />
	<!-- button type='button' onclick='attendInquiry({$solicitud_llamada->inquiry_ID})' style='width:20%;'>+</button -->
	<div id='opcionesAdminCallMePlease_{$solicitud_llamada->inquiry_ID}' class="contenedorOpcionesSolicitud elementoSombreado" >
		<div class='infoContainer'>
			<input name='origin_url[]' value='{l s='URL origen'}: {$solicitud_llamada->origin_url}' class='adminInputOriginURL elementoSombreado' />
			{if $employee}
				<input name='employee[]' value='{l s='Encargado'}: {$employee}' class='adminInputOriginURL elementoSombreado' />
			{/if}
		</div>
		<button type='button' onclick='attendInquiry({$solicitud_llamada->inquiry_ID})' class="btOpciones elementoSombreado">{l s='Atendiendo'}</button>
		<button type='button' onclick='customerHasBeenAttended({$solicitud_llamada->inquiry_ID})' class="btOpciones elementoSombreado">{l s='Finalizado'}</button>

		<div class="capaMasInfo">
			&nbsp;
			<div>
				<button type='button' class="elementoSombreado masterMoreOptionsBt">{l s='+'}</button>
				<div id='infoAdminCallMePlease_{$solicitud_llamada->inquiry_ID}' class="contenedorMasInfoSolicitud elementoSombreado">
						
						{comentarios_function}
						 
						 
					
					<form target="_blank" method="get" action="{$callmeplease_edit_path}" onsubmit="submitSearchForCustomer(this, {$solicitud_llamada->inquiry_ID})">
						<label class="labelBuscar">{l s='Buscar por...'}</label>
						<button onclick="registerButtonInSearchForCustomerFrom(this.form, this)" type='submit' id='ip_{$solicitud_llamada->inquiry_ID})' class="btMoreOptions elementoSombreado btMoreOptions1">{l s='IP'}</button>
						<button onclick="registerButtonInSearchForCustomerFrom(this.form, this)" type='submit' id='session_{$solicitud_llamada->inquiry_ID})' class="btMoreOptions elementoSombreado btMoreOptions2">{l s='Session ID'}</button>
						<button onclick="registerButtonInSearchForCustomerFrom(this.form, this)" type='submit' id='phone_{$solicitud_llamada->inquiry_ID})' class="btMoreOptions elementoSombreado btMoreOptions3">{l s='Phone'}</button>
						<button type='button' onclick='window.open("{$callmeplease_edit_path}&inquiry_ID={$solicitud_llamada->inquiry_ID}&updatecallme_please=true","_blank")' class="btMoreOptions elementoSombreado btMoreOptions4">{l s='Editar solicitud'}</button>
						
						<input name="submitFilter" value="1" type="hidden" />
						<input name='callme_pleaseFilter_cmpd!ip' id="IP_input_{$solicitud_llamada->inquiry_ID}" type="hidden" value="{$inquiry_data->ip}" disabled/>
						<input name='callme_pleaseFilter_cmpd!session_id' id="SESSION_input_{$solicitud_llamada->inquiry_ID}" type="hidden" value="{$inquiry_data->session_id}" disabled/>
						<input name='callme_pleaseFilter_a!customer_phone' id="PHONE_input_{$solicitud_llamada->inquiry_ID}" type="hidden" value="{$solicitud_llamada->customer_phone}" disabled/>
					</form>
				</div>	
			</div>
		</div>
	</div>
</div>
