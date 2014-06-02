{extends file='frontend/index/index.tpl'}

{* Breadcrumb *}
{block name='frontend_index_start' prepend}
	{$sBreadcrumb = [['name'=>{$sSupport.name}, 'link'=>{url controller=ticket sFid=$sSupport.id}]]}
{/block}

{* Sidebar left *}
{block name='frontend_index_content_left'}
	{include file="frontend/index/sidebar.tpl"}
{/block}

{* Main content *}
{block name='frontend_index_content'}
	<div class="forms--content content block panel right">

		{* Forms headline *}
		{block name='frontend_forms_index_headline'}
			<div class="forms--headline panel--body is--wide">
				{if $sSupport.sElements}
					<h1>{$sSupport.name}</h1>
					{eval var=$sSupport.text}
				{elseif $sSupport.text2}
					{include file="frontend/_includes/messages.tpl" type="success" content=$sSupport.text2}
				{/if}
			</div>
		{/block}

		{* Forms Content *}
		{block name='frontend_forms_index_content'}
			{if $sSupport.sElements}
				<div class="forms--container panel has--border">
					<h1 class="panel--title is--underline">{$sSupport.name}</h1>
					<div class="panel--body">
						{block name='frontend_forms_index_elements'}
							{include file="frontend/forms/elements.tpl"}
						{/block}
					</div>
				</div>
			{/if}
		{/block}

	</div>
{/block}

{* Hide sidebar right *}
{block name='frontend_index_content_right'}{/block}
