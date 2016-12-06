{**
 *   2009-2016 ohmyweb!
 *
 *   @author	ohmyweb <contact@ohmyweb.fr>
 *   @copyright 2009-2016 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
*}

{if isset($products) && $products}
<div id="idTabHupi" class="bullet">
    {include file="$tpl_dir./product-list.tpl" class='hupirecommend tab-pane' id='hupirecommend'}
</div>
{/if}
