<?xml version="1.0" encoding="{encoding}" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<atom:link href="{$sCategoryContent.rssFeed|rewrite:$sCategoryContent.description|escape}" rel="self" type="application/rss+xml" />
<title>{block name='frontend_atom_title'}{$sCategoryContent.description}{/block}</title>
<link>{url controller='index'}</link>
<description>{$sShopname} - {$sCategoryContent.description}</description>
<language>de-de</language>
<lastBuildDate>{time()|date:rss}</lastBuildDate>
{foreach from=$sArticles item=sArticle key=key name="counter"}
<item> 
	<title>{block name='frontend_listing_rss_article_name'}{$sArticle.articleName|escape}{/block}</title>
	<guid>{block name='frontend_listing_rss_guid'}{$sArticle.linkDetails|rewrite:$sArticle.articleName|escape}{/block}</guid>
	<link>{block name='frontend_listing_rss_link'}{$sArticle.linkDetails|rewrite:$sArticle.articleName|escape}{/block}</link>
	<description>{block name='frontend_listing_rss_description'}{$sArticle.description_long|strip_tags|strip|truncate:280:"...":true|escape}{/block}</description>
	<category>{block name='frontend_listing_rss_category'}{$sArticle.supplierName|escape}{/block}</category>
{if $sArticle.changetime}
	<pubDate>{block name='frontend_listing_rss_date'}{$sArticle.changetime|date:rss}{/block}</pubDate>
{/if}
</item>
{/foreach}
</channel>
</rss>