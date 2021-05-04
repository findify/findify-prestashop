{**
 * AM FINDIFY
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 *}

{extends file=$layout}
{block name='content'}
  <section id="main">

    <div data-findify="search">
      <div class="findify-component-spinner"></div>
      <div class="findify-fallback" style="display:none;">
        <section id="products" class="tratratrta">
          {if $listing.products|count}

            <div>
              {block name='product_list_top'}
                {include file='catalog/_partials/products-top.tpl' listing=$listing}
              {/block}
            </div>

            {block name='product_list_active_filters'}
              <div id="" class="hidden-sm-down">
                {$listing.rendered_active_filters nofilter}
              </div>
            {/block}

            <div>
              {block name='product_list'}
                {include file='catalog/_partials/products.tpl' listing=$listing}
              {/block}
            </div>

            <div id="js-product-list-bottom">
              {block name='product_list_bottom'}
                {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
              {/block}
            </div>

          {else}
            <div id="js-product-list-top"></div>

            <div id="js-product-list">
              {include file='errors/not-found.tpl'}
            </div>

            <div id="js-product-list-bottom"></div>
          {/if}
        </section>
      </div>
    </div>

  </section>
{/block}
