{extends file="frontend/listing/box_article.tpl"}

{* Product actions *}
{block name='frontend_listing_box_article_actions_inner'}
    <div class="actions">
        <a href="{$sArticle.linkDetails|rewrite:$sArticle.articleName}" title="{s name='SimilarBoxMore'}{/s} {$sArticle.articleName}" class="more">{se name='SimilarBoxLinkDetails'}{/se}</a>
    </div>
{/block}

{* Product description *}
{block name='frontend_listing_box_article_description'}
    {include file="frontend/listing/product-box/description.tpl" size=47}
{/block}