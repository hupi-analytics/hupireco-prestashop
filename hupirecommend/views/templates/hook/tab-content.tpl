{**
 *   2009-2016 ohmyweb!
 *
 *   @author	ohmyweb <contact@ohmyweb.fr>
 *   @copyright 2009-2016 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
*}

{if isset($products) && $products}
<div id="hupirecommend_container" class="page-product-box">
    <h3 class="page-product-heading">{l s='We recommend' mod='hupirecommend'}</h3>
	{include file="$tpl_dir./product-list.tpl" class='hupirecommend tab-pane' id='hupirecommend'}
</div>
{/if}