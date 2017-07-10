{*
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
*  @author MANU PARA TELEROSA.COM / CORONAFUNERAL.COM
*  @copyright  2007-2015 MANU
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="contact-link">
	<div class="posicion_relativa">
		<a class="animacion_contactar_zoomInRight_zoomOutDown posicion_absoluta contact_division texto texto_parrafo_barra_contacto" href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='Contact us' mod='blockcontactmanu'}">{l s='Contact us' mod='blockcontactmanu'}</a>
{if $callmeplease_activated == '1'}
	{if $showntelnumber}
		{if $telnumber}
			<span class="shop-phone animacion_telefono_zoomInLeft_zoomOutDown posicion_absoluta texto_parrafo_barra_small" style="display: initial">
				<a class="texto" href="tel:{$telnumber}">
				
					<i class="icon-phone"></i>{*l s='Llámanos:' mod='blockcontactmanu'*} <strong>{$showntelnumber}</strong>
				</a>
			</span>
		{else}
			<span class="shop-phone animacion_telefono_zoomInLeft_zoomOutDown posicion_absoluta texto texto_parrafo_barra_small">
				<i class="icon-phone"></i>{*l s='Llámanos:' mod='blockcontactmanu'*} <strong>{$showntelnumber}</strong>
			</span>
		{/if}
	{/if}
{else}
	<style>
		.animacion_telefono_zoomInLeft_zoomOutDown {
			-webkit-animation-duration: 1s !important;
			animation-duration: 1s !important;
		}
		.animacion_contactar_zoomInRight_zoomOutDown {
			-webkit-animation-duration: 5s !important;
			animation-duration: 5s !important;
		}
	</style>
{/if}	
	</div>
</div>

<script>
	var callmeplease_path = '{$callmeplease_path}';
</script>

{if $callmeplease_activated == '1'}
{*}
	<div id="container-callme-please">
		<a class="posicion_relativa" id="one-callme-please" title="{l s='Click here to receive a call from us' mod='blockcontactmanu'}">
			<span class="posicion_absoluta texto bounceInLeft-Out texto_parrafo_barra_contacto">{l s='Llamada GRATUITA' mod='blockcontactmanu'}</span>
			<span class="posicion_absoluta texto bounceInLeft-Out2 texto_parrafo_barra_contacto">{l s='Pulsa aquí y te llamamos' mod='blockcontactmanu'}</span>
			<span class="posicion_absoluta texto bounceInLeft-Out3 texto_parrafo_barra_contacto">{l s='ABIERTO AHORA' mod='blockcontactmanu'}</span>
		</a>
		<div id="two-callme-please">
			<div id="callme-menu-container">
				<div id='callmeform'>
					<div class="buttom_call-me-please" id="close_div-callme-please" >
						<span id="close_buttom-callme-please"> X </span> 
					</div>
					{l s='Su nombre'} 
					<br />
					<input style="width:90%;" id='customer_name' name='customer_name' />
					<br />
					{l s='Su teléfono'} 
					<br />
					<input style="width:90%;" id='customer_phone' name='customer_phone'/>
					<br />
					{l s='Su código de país'} 
					<br />
					<input style="width:90%;" id='customer_country' name='customer_country' value='{l s='0034 España'}' />
					<br />
					* {'un operador le llamará en los próximos minutos'}
					<br />
					<button type='button' id="bt_enviar-callme-please" class="buttom_call-me-please">Llámame!</buton>
				</div>
			</div>
		</div>
	</div>
{*}	
{/if}
