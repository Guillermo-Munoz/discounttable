{if isset($discount_rules) && $discount_rules|count > 0}
<section class="product-discounts-horizontal">
    <div class="table-title">{$dt_title}</div>
    
    <div class="table-responsive">
        <table>
            <tbody>
                <tr class="row-names">
                    <td class="table-label">{l s='Unidades' mod='discounttable'}</td>
                    {foreach from=$discount_rules item=rule}
                        <td>{$rule.rule_name}</td>
                    {/foreach}
                </tr>
                <tr class="row-values">
                    <td class="table-label">{l s='Descuento' mod='discounttable'}</td>
                    {foreach from=$discount_rules item=rule}
                        <td style="color: {$dt_color} !important;">{$rule.discount}</td>
                    {/foreach}
                </tr>
            </tbody>
        </table>
    </div>
</section>
{/if}