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