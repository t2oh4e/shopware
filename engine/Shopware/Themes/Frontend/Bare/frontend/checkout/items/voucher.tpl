{namespace name="frontend/checkout/cart_item"}

<div class="table--row block-group row--voucher{if $isLast} is--last-row{/if}">

    {* Product information column *}
    {block name='frontend_checkout_cart_item_voucher_name'}
        <div class="table--column column--product block">

			{* Badge *}
			{block name='frontend_checkout_cart_item_voucher_badge'}
				<div class="table--media">
					<div class="basket--badge">
						<i class="icon--coupon"></i>
					</div>
				</div>
			{/block}

            {* Product information *}
            {block name='frontend_checkout_cart_item_voucher_details'}
                <div class="table--content">

                    {* Product name *}
                    {block name='frontend_checkout_cart_item_voucher_details_title'}
                        <span class="content--title">{$sBasketItem.articlename|strip_tags|truncate:60}</span>
                    {/block}

                    {* Product SKU number *}
                    {block name='frontend_checkout_cart_item_voucher_details_sku'}
                        <p class="content--sku content">
                            {s name="CartItemInfoId"}{/s} {$sBasketItem.ordernumber}
                        </p>
                    {/block}

                    {* Additional product information *}
                    {block name='frontend_checkout_cart_item_voucher_details_inline'}{/block}
                </div>
            {/block}
        </div>
    {/block}

    {* Product tax rate *}
    {block name='frontend_checkout_cart_item_voucher_tax_price'}{/block}

    {* Accumulated product price *}
    {block name='frontend_checkout_cart_item_voucher_total_sum'}
        <div class="table--column column--total-price block is--align-right">
			{block name='frontend_checkout_cart_item_voucher_total_sum_label'}
				<div class="column--label total-price--label">
					{s name="CartColumnTotal" namespace="frontend/checkout/cart_header"}{/s}
				</div>
			{/block}

            {block name='frontend_checkout_cart_item_voucher_total_sum_display'}
                {if $sBasketItem.itemInfo}
                    {$sBasketItem.itemInfo}
                {else}
                    {$sBasketItem.price|currency}{block name='frontend_checkout_cart_tax_symbol'}{s name="Star" namespace="frontend/listing/box_article"}{/s}{/block}
                {/if}
            {/block}
        </div>
    {/block}

    {* Remove voucher from basket *}
    {block name='frontend_checkout_cart_item_voucher_delete_article'}
        <div class="table--column column--actions block">
            <a href="{url action='deleteArticle' sDelete=voucher sTargetAction=$sTargetAction}" class="btn is--small" title="{"{s name='CartItemLinkDelete '}{/s}"|escape}">
				<i class="icon--cross"></i>
            </a>
        </div>
    {/block}
</div>