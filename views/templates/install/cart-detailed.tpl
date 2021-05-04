{**
 * AM FINDIFY
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 *}

{block name='cart_detailed_product'}
  <div class="cart-overview js-cart" data-refresh-url="{url entity='cart' params=['ajax' => true, 'action' => 'refresh']}">
    <div data-findify-event="update-cart">
    {if $cart.products}
      {foreach from=$cart.products item=product}
        {assign var="p_price" value=$product.price|replace:',':'.'}
        <div data-findify-item-id={$product.id_product} data-findify-variant-item-id={$product.id_product_attribute} data-findify-unit-price="{{preg_replace('/[^0-9-.]+/', '', $p_price)}}" data-findify-quantity="{{$product.quantity}}"></div>
      {/foreach}
    {/if}
  </div>
    {if $cart.products}
    <div class="cart-items">
      {foreach from=$cart.products item=product}
        <div class="cart-item">
          {block name='cart_detailed_product_line'}
            {include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}
          {/block}
        </div>
        {if is_array($product.customizations) && $product.customizations|count >1}<hr>{/if}
      {/foreach}
    </div>
    {else}
      <span class="no-items">{l s='There are no more items in your cart' d='Shop.Theme.Checkout'}</span>
    {/if}
  </div>
{/block}
