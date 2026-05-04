{foreach $options as $name => $values}
    <p>{('ms2_product_' ~ $name) | lexicon} {if $name == 'vyisota' || $name == 'shirina'}(мм){else}{/if}:</p>
    <div class="radio-container"
        {if $constraints[$name]}
            data-constraints="{$constraints[$name]| json_encode:256 | htmlentities}"
        {/if}>
        {foreach $values as $value index=$index}
            <label class="input-parent radio-label"
                   itemprop="variant"
                   content="{if $name == 'vyisota' || $name == 'shirina'}{$values[$index]} мм{else}{$values[$index]}{/if}">
                <input type="radio"
                       class="radio-input"
                       value="{$values[$index]}"
                       name="options[{$name}]"
                       {if $index == 0}checked="checked"{/if}
                       {if $constraints[$name]}
                           data-relations="{$relations[$name][$value]| json_encode:256 | htmlentities}"
                       {/if}
                />
                {if $name == 'vyisota' || $name == 'shirina'}{$values[$index]} мм{else}{$values[$index]}{/if}
            </label>
        {/foreach}
    </div>
{/foreach}