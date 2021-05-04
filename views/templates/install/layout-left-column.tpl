{**
 * AM FINDIFY
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 *}

{extends file='layouts/layout-both-columns.tpl'}

{block name='right_column'}{/block}

{block name='content_wrapper'}
	{if $page.page_name !== 'category'}
		<div id="content-wrapper" class="left-column col-xs-12 col-sm-8 col-md-9">
	{else}
		<div id="content-wrapper" class="left-column col-xs-12 col-sm-12 col-md-12">
	{/if}
			{hook h="displayContentWrapperTop"}
			{block name='content'}
			<p>Hello world! This is HTML5 Boilerplate.</p>
			{/block}
			{hook h="displayContentWrapperBottom"}
		</div>
{/block}
