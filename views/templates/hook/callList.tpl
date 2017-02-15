<script>
	var callmeplease_path = '{$callmeplease_path}';
	var sonido = '{$sonido_activado}';
	var inquiry_hash_array = [];
	{foreach from=$pending_calls item=solicitud_llamada}
		inquiry_hash_array[{$solicitud_llamada->inquiry_ID}] = '{$solicitud_llamada->hash}';
	{/foreach}

</script>
<div id="commentDialog"><textarea id="commentTextArea" ></textarea></div>
<input type="hidden" value="{$employee}" id="employee" />
<div id='backOfficeCallmeWindow'>
	<audio id='callmePleaseAlarm' src='{$alert_sound_url}' preload='auto'></audio>
	<div id='minimize_button' class='elementoSombreado' onclick='toggleCallList()'> - </div>
	{if $sonido_activado == 'true'}
		<button onclick='toggleSound()' type='button' id='btSonido' class='elementoSombreado btAdminCallMePlease btAdminCallMePleaseRed'>{l s='desactivar sonido'}</button>
	{else}
		<button onclick='toggleSound()' type='button' id='btSonido' class='elementoSombreado btAdminCallMePlease btAdminCallMePleaseGreen'>{l s='activar sonido'}</button>
	{/if}
	{if $callmeplease_activated == '1'}
		<button id='btChangeCallStatus' onclick='toggleCallStatus()' type='button' class='elementoSombreado btAdminCallMePlease btAdminCallMePleaseRed'>{l s="desactivar llamadas"}</button>
	{else}
		<button id='btChangeCallStatus' onclick='toggleCallStatus()' type='button' class='elementoSombreado btAdminCallMePlease btAdminCallMePleaseGreen'>{l s="activar llamadas"}</button>
	{/if}
	<div id='callListContainer'>
		{foreach from=$pending_calls item=solicitud_llamada}
			{*include file="$callmepleaseLineTPL" llamada='solicitud_llamada' *}
			{$solicitud_llamada->inquiry_admin_code}
		{/foreach}
	</div>
</div>