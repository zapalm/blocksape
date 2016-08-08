{**
 * Sape block: module for PrestaShop 1.2-1.6
 *
 * @author    zapalm <zapalm@ya.ru>
 * @copyright 2013-2016 zapalm
 * @link      http://prestashop.modulez.ru/en/free-products/36-sapient-solution-sape.html The module's homepage
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{if !empty($ad_link)}
	{if isset($footer)}
		<div class="footer-ad">
			{l s='Advertisement' mod='blocksape'}: {$ad_link}
		</div>		
	{else}
		<div class="block">
			<h4>{l s='Advertisement' mod='blocksape'}</h4>
			<div class="block_content">
				{$ad_link}
			</div>
		</div>
	{/if}
{/if}