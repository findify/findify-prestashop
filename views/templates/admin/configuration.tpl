{**
 * AM FINDIFY
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 *}

<div class="panel">

   <h3>{l s='Product feed export' mod='findify'}</h3>
   <p>{l s='You have two ways to export your product feed.' mod='findify'}</p>
   <p>1. <strong>{l s='Manually :' mod='findify'}</strong> {l s='Click on the url below' mod='findify'} : <br><a href="{$amf_cron|escape:'htmlall':'UTF-8'}" target="_blank">{$amf_cron|escape:'htmlall':'UTF-8'}</a></p>
   <p><span style="font-style: italic;">{l s='-or-' mod='findify'}</p>
   <p>2. <strong>{l s='Automatically :' mod='findify'}</strong> {l s='Ask your hosting provider to setup a "Cron job" to load the following URL at the time you would like:' mod='findify'}<br><a href="{$amf_cron|escape:'htmlall':'UTF-8'}" target="_blank">{$amf_cron|escape:'htmlall':'UTF-8'}</a><br>{l s='It will automatically export the product feed.' mod='findify'}<br>{l s='You should scheduling the task one time a day for example at 01h00.' mod='findify'}</p>

</div>
{if isset($amf_feed_url) && $amf_feed_url != ''}
   <div class="panel">

      <h3>{l s='Product feed url' mod='findify'}</h3>
      <p>{l s='Here is your product feed url you can send to Findify :' mod='findify'}</p>
      {if !$amf_feed_url}
         <p class="alert alert-warning">{l s='No product feed available. You need to export product feed first above.' mod='findify'}</p>
      {else}
         {foreach from=$amf_feed_url key=k item=feed}
            <p><a href="{$feed|escape:'htmlall':'UTF-8'}" target="_blank">{$feed|escape:'htmlall':'UTF-8'}</a></p>
         {/foreach}
      {/if}
      
   </div>
{/if}
<div class="panel">

   <h3>{l s='Integration in your theme' mod='findify'}</h3>
   <p class="alert alert-warning">{l s='Before copiyng files, please do a backup of your theme files.' mod='findify'}<br>{l s='You can also modify your theme files from the Findify documentation :' mod='findify'} <a target="_blank" href="https://developers.findify.io/docs/prestashop">https://developers.findify.io/docs/prestashop</a></p>

   <form id="amfinddify_intergration1" class="form-horizontal amfinddify_intergration1" action="{$action}" method="post">
      <input type="hidden" name="type" value="1">
      <input type="hidden" name="file" value="cart-detailed.tpl">
      <p>
         <span class="label{if !isset($type1) && !$type1} label-default{else} label-success{/if}">1. {l s='Add \'update-cart\' tags to the Cart Page in /templates/checkout/_partials/cart-detailed.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type1) && !$type1}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form>

   <hr>

   <form id="amfinddify_intergration2" class="form-horizontal amfinddify_intergration2" action="{$action}" method="post">
      <input type="hidden" name="type" value="2">
      <input type="hidden" name="file" value="order-confirmation-table.tpl">
      <p>
         <span class="label{if !isset($type2) && !$type2} label-default{else} label-success{/if}">2. {l s='Add \'purchase-tracking\' tags to the Order Confirmation Page in /templates/checkout/_partials/order-confirmation-table.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type2) && !$type2}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form> 

   <hr>

   <form id="amfinddify_intergration3" class="form-horizontal amfinddify_intergration3" action="{$action}" method="post">
      <input type="hidden" name="type" value="3">
      <input type="hidden" name="file" value="product-list.tpl">
      <p>
         <span class="label{if !isset($type3) && !$type3} label-default{else} label-success{/if}">3. {l s='Create A Search Results Page in /templates/catalog/listing/product-list.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type3) && !$type3}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form>   

   <hr>

   <form id="amfinddify_intergration4" class="form-horizontal amfinddify_intergration4" action="{$action}" method="post">
      <input type="hidden" name="type" value="4">
      <input type="hidden" name="file" value="category-product-list.tpl">
      <p>
         <span class="label{if !isset($type4) && !$type4} label-default{else} label-success{/if}">4. {l s='Create a new file for Product listing in /templates/catalog/listing/category-product-list.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type4) && !$type4}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form>   

   <hr>

   <form id="amfinddify_intergration5" class="form-horizontal amfinddify_intergration5" action="{$action}" method="post">
      <input type="hidden" name="type" value="5">
      <input type="hidden" name="file" value="category.tpl">
      <p>
         <span class="label{if !isset($type5) && !$type5} label-default{else} label-success{/if}">5. {l s='Modify the category file in /templates/catalog/listing/category.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type5) && !$type5}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form> 

   <hr>

   <form id="amfinddify_intergration6" class="form-horizontal amfinddify_intergration6" action="{$action}" method="post">
      <input type="hidden" name="type" value="6">
      <input type="hidden" name="file" value="layout-both-columns.tpl">
      <p>
         <span class="label{if !isset($type6) && !$type6} label-default{else} label-success{/if}">6. {l s='Removing sidebars from category pages (if applicable) in /templates/layouts/layout-both-columns.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type6) && !$type6}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form>

   <hr>

   <form id="amfinddify_intergration7" class="form-horizontal amfinddify_intergration7" action="{$action}" method="post">
      <input type="hidden" name="type" value="7">
      <input type="hidden" name="file" value="layout-left-column.tpl">
      <p>
         <span class="label{if !isset($type7) && !$type7} label-default{else} label-success{/if}">7. {l s='Removing sidebars from category pages (if applicable) in /templates/layouts/layout-left-column.tpl file' mod='findify'}</span>
      </p>
      {if !isset($type7) && !$type7}<button type="submit" value="1" id="submit_type" name="submit_type" class="btn btn-default"><i class="icon-stack icon-pencil"></i>{l s='Copy the file with tag to your theme' mod='findify'}</button>{/if}
   </form>

</div>
<div class="panel">

   <h3>{l s='Images formats' mod='findify'}</h3>
   <p>{l s='You need to create 2 new images formats in Back Office > Design > Image Settings :' mod='findify'}
   <ul>
      <li>{l s='Format name : "findify-image" ; Image size : 180px 180px.' mod='findify'}</li>
      <li>{l s='Format name : "findify-thumbnail" ; Image size : 65px 65px.' mod='findify'}</li>
   </ul>

   <p class="alert alert-warning">{l s='More info from the Findify documentation :' mod='findify'} <a target="_blank" href="https://developers.findify.io/docs/feed-generation-manual-csv">https://developers.findify.io/docs/feed-generation-manual-csv</a><br>{l s='You can use this module to regenerate thumbnails if you have a large catalog :' mod='findify'} <a target="_blank" href="https://addons.prestashop.com/en/fast-mass-updates/19228-regenerate-thumbnails-images-for-large-catalogues.html">https://addons.prestashop.com/en/fast-mass-updates/19228-regenerate-thumbnails-images-for-large-catalogues.html</a></p>

</div>
<div class="panel">

   <h3>{l s='Logs' mod='findify'}</h3>
   <p>{l s='You can check logs by clicking on the links :' mod='findify'}</p>
   <p>
      {foreach from=$logs key=k item=log}
         <a href="{$log.url|escape:'htmlall':'UTF-8'}" target="_blank">{$log.filename|escape:'htmlall':'UTF-8'}</a><br>
      {/foreach}    
   </p>
   <p>{l s='Log files are kept for 30 days and deleted automatically. ' mod='findify'}</p>

</div>
