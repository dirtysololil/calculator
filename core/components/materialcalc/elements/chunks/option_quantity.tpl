<input type="hidden" id="msal_price_original" value="{$_modx->getPlaceholder('price')}" class="msoptionsprice-{$_modx->resource.id}">
<input type="hidden" id="msal_hash" value="{$hash}" name="msal_key">
<input type="hidden" id="msal_show_cost" value="{$show_cost}" name="msal_show_cost">
{foreach $inputs as $input}
<div class="sm-text option-title">
    <b>{$input.pagetitle}:</b>
</div>
<div class="shelf-control">
<div class="quantity_inner">        
    <button type="button" class="bt_minus" onclick="changeQuantity(this, -1)">
        <svg viewBox="0 0 24 24" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    </button>
    <input type="text" min="2" name="{$input.input_name}" id="msal_{$input.id}" data-price="{$input.price}" data-discount="{$input.discount != '' ? $input.discount : ' '}" value="{if $input.value}{$input.value}{else}0{/if}" data-product-id="[[*id]]"  class="quantity msal_input"/>
    <button type="button" class="bt_plus" onclick="changeQuantity(this, 1)">
        <svg viewBox="0 0 24 24" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    </button>         
</div>
</div>
{/foreach}