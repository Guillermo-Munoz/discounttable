<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Ajustes de la Tabla' mod='discounttable'}
    </div>
    <form action="{$action_url}" method="post" class="form-horizontal">
        <div class="panel-body">
            
            {* 1. Título de la tabla *}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Título de la tabla' mod='discounttable'}</label>
                <div class="col-lg-6">
                    <input type="text" name="DT_TITLE" value="{$DT_TITLE|escape:'html':'UTF-8'}" class="form-control">
                </div>
            </div>

            {* 2. Categoría Activa - Recuperada *}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Categoría activa' mod='discounttable'}</label>
                <div class="col-lg-6">
                    <select name="DT_CATEGORY_ID" class="form-control">
                        <option value="0">{l s='-- Todas las categorías --' mod='discounttable'}</option>
                        {foreach from=$categories item=category}
                            <option value="{$category.id_category}" {if isset($DT_CATEGORY_ID) && $category.id_category == $DT_CATEGORY_ID}selected="selected"{/if}>
                                {$category.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                </div>
            </div>

            {* 3. Color del Descuento *}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Color del descuento' mod='discounttable'}</label>
                <div class="col-lg-2">
                    <input type="color" name="DT_COLOR" value="{$DT_COLOR|escape:'html':'UTF-8'}" class="form-control" style="height: 35px;">
                </div>
            </div>

            {* 4. Máximo de reglas - Recuperado *}
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Máximo de reglas a mostrar' mod='discounttable'}</label>
                <div class="col-lg-2">
                    <input type="number" name="DT_LIMIT" value="{$DT_LIMIT|intval}" class="form-control">
                </div>
            </div>

        </div>
        <div class="panel-footer">
            <button type="submit" name="submitDiscountTable" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Guardar configuración' mod='discounttable'}
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-heading"><i class="icon-list"></i> {l s='Reglas Activas' mod='discounttable'}</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{l s='Nombre' mod='discounttable'}</th>
                    <th>{l s='Descuento' mod='discounttable'}</th>
                    <th class="text-right">{l s='Acciones' mod='discounttable'}</th>
                </tr>
            </thead>
            <tbody>
                {if isset($active_rules) && $active_rules|count > 0}
                    {foreach from=$active_rules item=rule}
                    <tr>
                        <td>{$rule.id_cart_rule|intval}</td>
                        <td><strong>{$rule.rule_name|escape:'html':'UTF-8'}</strong></td>
                        <td>{$rule.discount|escape:'html':'UTF-8'}</td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="{$admin_cart_rules_link}&id_cart_rule={$rule.id_cart_rule|intval}&updatecart_rule=1" class="btn btn-default">
                                    <i class="icon-pencil"></i> {l s='Editar' mod='discounttable'}
                                </a>
                                <a href="{$admin_cart_rules_link}&id_cart_rule={$rule.id_cart_rule|intval}&deletecart_rule=1" class="btn btn-danger" onclick="return confirm('{l s='¿Borrar?' mod='discounttable'}');">
                                    <i class="icon-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                {else}
                    <tr><td colspan="4" class="text-center">{l s='No hay reglas' mod='discounttable'}</td></tr>
                {/if}
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <a href="{$admin_cart_rules_link}&addcart_rule=1" class="btn btn-primary">
            <i class="process-icon-new"></i> {l s='Crear nueva regla' mod='discounttable'}
        </a>
    </div>
</div>