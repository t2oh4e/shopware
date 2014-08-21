{if !$sUserData.additional.charge_vat}
	{assign var="sRealAmount" value=$sAmountNet|replace:",":"."}
{else}
	{if $sAmountWithTax}
		{assign var="sRealAmount" value=$sAmountWithTax|replace:",":"."}
	{else}
		{assign var="sRealAmount" value=$sAmount|replace:",":"."}
	{/if}
{/if}
{if {config name=TSID}}
	{block name='trusted_shops_form'}
		<div class="trusted-shops--form">

			<div class="panel">
				<form name="formSiegel" method="post" action="https://www.trustedshops.com/shop/certificate.php" target="_blank">
					<input type="image" src="{link file='frontend/_public/src/img/logos/logo--trusted-shops-big.gif'}" title="{"{s name='WidgetsTrustedShopsHeadline'}{/s}"|escape}" />
					<input name="shop_id" type="hidden" value="{config name=TSID}" />
				</form>
			</div>

			<div class="panel">
				<form id="formTShops" name="formTShops" method="post" action="https://www.trustedshops.com/shop/protection.php" target="_blank">
					<input name="_charset_" type="hidden" value="{encoding}">
					<input name="shop_id" type="hidden" value="{config name=TSID}">
					<input name="email" type="hidden" value="{$sUserData.additional.user.email}">
					<input name="amount" type="hidden" value="{$sRealAmount}">
					<input name="curr" type="hidden" value="{config name=currency}">

					{* Payment type *}
					{*  <input name="paymentType" type="hidden" value="{ value paymentType}"> *}
					<input name="kdnr" type="hidden" value="{$sUserData.billingaddress.customernumber}">
					<input name="ordernr" type="hidden" value="{$sOrderNumber}">

					{* Descriptiontext *}
					<p>{s name='WidgetsTrustedShopsText'}{/s}</p>

					<button type="submit" class="btn btn--secondary" name="btnProtect">
                        {s name='WidgetsTrustedShopsInfo'}{/s}
                    </button>
				</form>
			</div>
		</div>
	{/block}
{/if}