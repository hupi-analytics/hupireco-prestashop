{**
 *   2009-2016 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright 2009-2016 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 *}


{*
<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.jpg" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='Lorem' mod='hupilytics'}</h2>
			<h4>{l s='Lorem ipsum dolor' mod='hupilytics'}</h4>
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<p>
					<h4>{l s='Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor' mod='hupilytics'}</h4>
					<ul class="ul-spaced">
						<li><strong>{l s='Lorem ipsum dolor sit amet' mod='hupilytics'}</strong></li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='hupilytics'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='hupilytics'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='hupilytics'}</li>
						<li>{l s='Lorem ipsum dolor sit amet' mod='hupilytics'}</li>
					</ul>
				</p>

				<br />

				<p class="text-center">
					<strong>
						<a href="http://www.prestashop.com" target="_blank" title="Lorem ipsum dolor">
							{l s='Lorem ipsum dolor' mod='hupilytics' }
						</a>
					</strong>
				</p>
			</div>
		</div>
	</div>
</div>
*}
{if isset($errorFile) && $errorFile}{$errorFile}{/if}

<form id="configuration_form" class="defaultForm form-horizontal hupirecommend" method="post" enctype="multipart/form-data" novalidate="">
    <div class="panel" id="fieldset_0">
        <div class="panel-heading">Configuration des recommandations</div>
        <div class="form-wrapper">
            <div class="row">
                <div class="form-group">
                    <label class="control-label col-lg-3">Token API</label>
                    <div class="col-lg-9">
                        <input name="hupireco_token" id="hupireco_token" value="{$hupireco_token}" class="form-control" type="text">
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <hr />
            
            <div class="row">
                <div class="col-lg-12">
                    <h4>Affichage sur fiche produit ?</h4>
                    <div class="row form-inline">
                        <div class="col-xs-12 col-lg-8">
                            <span class="switch prestashop-switch fixed-width-lg pull-left">
                                <input name="product_page[active]" id="product_page_on" value="1"{if $product_page.active} checked="checked"{/if} type="radio">
                                <label for="product_page_on">Activé</label>
                                <input name="product_page[active]" id="product_page_off" value="0"{if !$product_page.active} checked="checked"{/if} type="radio">
                                <label for="product_page_off">Désactivé</label> 
                                <a class="slide-button btn"></a>
                            </span>
                            <span class="m-r-l">
                                <label>End Point : <input type="text" name="product_page[end_point]" class="form-control" value="{$product_page.end_point}" /></label>
                            </span>
                            <span>
                                <label>Nombre de produits : <input type="text" name="product_page[nb_products]" class="form-control fixed-width-sm" value="{$product_page.nb_products}" /></label>
                            </span>
                        </div>
                    </div>
                    <!-- /input-group -->
                </div>
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->

            <hr />
            
            <div class="row">
                <div class="col-lg-12">
                    <h4>Affichage sur le panier ?</h4>
                    <div class="row form-inline">
                        <div class="col-xs-12 col-lg-8">
                            <span class="switch prestashop-switch fixed-width-lg pull-left">
                                <input name="shopping_cart[active]" id="shopping_cart_on" value="1"{if $shopping_cart.active} checked="checked"{/if}type="radio">
                                <label for="shopping_cart_on">Activé</label>
                                <input name="shopping_cart[active]" id="shopping_cart_off" value="0"{if !$shopping_cart.active} checked="checked"{/if} type="radio">
                                <label for="shopping_cart_off">Désactivé</label> 
                                <a class="slide-button btn"></a>
                            </span>
                            <span class="m-r-l">
                                <label>End Point : <input type="text" name="shopping_cart[end_point]" class="form-control" value="{$shopping_cart.end_point}" /></label>
                            </span>
                            <span>
                                <label>Nombre de produits : <input type="text" name="shopping_cart[nb_products]" class="form-control fixed-width-sm" value="{$shopping_cart.nb_products}" /></label>
                            </span>
                        </div>
                    </div>
                    <!-- /input-group -->
                </div>
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->

            <hr />
            
            <div class="row">
                <div class="col-lg-12">
                    <h4>Affichage sur la page d'accueil ?</h4>
                    <div class="row form-inline">
                        <div class="col-xs-12 col-lg-8">
                            <span class="switch prestashop-switch fixed-width-lg pull-left">
                                <input name="homepage[active]" id="homepage_on" value="1"{if $homepage.active} checked="checked"{/if} type="radio">
                                <label for="homepage_on">Activé</label>
                                <input name="homepage[active]" id="homepage_off" value="0"{if !$homepage.active} checked="checked"{/if} type="radio">
                                <label for="homepage_off">Désactivé</label> 
                                <a class="slide-button btn"></a>
                            </span>
                            <span class="m-r-l">
                                <label>End Point : <input type="text" name="homepage[end_point]" class="form-control" value="{$homepage.end_point}" /></label>
                            </span>
                            <span>
                                <label>Nombre de produits : <input type="text" name="homepage[nb_products]" class="form-control fixed-width-sm" value="{$homepage.nb_products}" /></label>
                            </span>
                        </div>
                    </div>
                    <!-- /input-group -->
                </div>
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.form-wrapper -->

        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="updateConfig" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> 
                Enregistrer
            </button>
        </div>
    </div>
</form>
