<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class DiscountTable extends Module
{
    public function __construct()
    {
        $this->name = 'discounttable';
        $this->tab = 'pricing_promotion';
        $this->version = '1.3.0';
        $this->author = 'Williams';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Tabla de Descuentos Pro');
        $this->description = $this->l('GestiÃ³n de reglas de carrito y tabla de descuentos para PS 9.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.9.9'];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayDiscountTable')
            && Configuration::updateValue('DT_CATEGORY_ID', 0)
            && Configuration::updateValue('DT_TITLE', 'Descuentos por volumen')
            && Configuration::updateValue('DT_COLOR', '#2fb5d2')
            && Configuration::updateValue('DT_LIMIT', 5);
    }

    public function getContent()
{
    $output = '';
    if (Tools::isSubmit('submitDiscountTable')) {
        Configuration::updateValue('DT_CATEGORY_ID', (int)Tools::getValue('DT_CATEGORY_ID'));
        Configuration::updateValue('DT_TITLE', Tools::getValue('DT_TITLE'));
        Configuration::updateValue('DT_COLOR', Tools::getValue('DT_COLOR'));
        Configuration::updateValue('DT_LIMIT', (int)Tools::getValue('DT_LIMIT'));
        
        $output .= $this->displayConfirmation($this->l('ConfiguraciÃ³n actualizada'));
    }

    // Forzamos que siempre sea un array, incluso si falla la consulta
    $active_rules = $this->getRulesForDisplay(20);
    if (!is_array($active_rules)) {
        $active_rules = [];
    }

    $this->context->smarty->assign([
        'DT_CATEGORY_ID' => (int)Configuration::get('DT_CATEGORY_ID'),
        'DT_TITLE' => Configuration::get('DT_TITLE') ? Configuration::get('DT_TITLE') : 'Descuentos',
        'DT_COLOR' => Configuration::get('DT_COLOR') ? Configuration::get('DT_COLOR') : '#000000',
        'DT_LIMIT' => (int)Configuration::get('DT_LIMIT'),
        'categories' => Category::getCategories((int)$this->context->language->id, true, false),
        'active_rules' => $active_rules,
        'action_url' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]),
        'admin_cart_rules_link' => $this->context->link->getAdminLink('AdminCartRules') // Enlace generado en PHP para evitar fallos en TPL
    ]);

    return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
}

public function hookDisplayDiscountTable($params)
{
    // Solo en p¨¢gina de producto principal
    $controller = Context::getContext()->controller;
    
    // Verificar si estamos en p¨¢gina de producto
    if (!isset($controller->php_self) || $controller->php_self != 'product') {
        return '';
    }
    
    // Obtener ID del producto de diferentes maneras
    $id_product = 0;
    
    // M¨¦todo 1: Desde los par¨¢metros
    if (isset($params['product']['id_product'])) {
        $id_product = (int)$params['product']['id_product'];
    }
    // M¨¦todo 2: Desde la URL
    elseif (Tools::getValue('id_product')) {
        $id_product = (int)Tools::getValue('id_product');
    }
    // M¨¦todo 3: Desde el controlador
    elseif (isset($controller->id_product)) {
        $id_product = (int)$controller->id_product;
    }
    
    if ($id_product <= 0) {
        return '';
    }
    
    // Validar categor¨ªa si est¨¢ configurada
    $selected_cat = (int)Configuration::get('DT_CATEGORY_ID');
    if ($selected_cat > 0) {
        $product_categories = Product::getProductCategories($id_product);
        if (!in_array($selected_cat, $product_categories)) {
            return '';
        }
    }

    // Obtener reglas de descuento
    $limit = (int)Configuration::get('DT_LIMIT');
    $rules = $this->getRulesForDisplay($limit);
    
    if (empty($rules)) {
        return '';
    }

    // Asignar variables al template
    $this->context->smarty->assign([
        'dt_title' => Configuration::get('DT_TITLE') ? Configuration::get('DT_TITLE') : 'Descuentos por volumen',
        'dt_color' => Configuration::get('DT_COLOR') ? Configuration::get('DT_COLOR') : '#2fb5d2',
        'discount_rules' => $rules
    ]);

    return $this->display(__FILE__, 'views/hook/displayProductDiscountTable.tpl');
}

    private function getRulesForDisplay($limit)
    {
        $id_lang = (int)$this->context->language->id;
        $sql = 'SELECT cr.id_cart_rule, crl.name as rule_name, cr.minimum_amount, cr.reduction_percent, cr.reduction_amount
                FROM ' . _DB_PREFIX_ . 'cart_rule cr
                LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ' . $id_lang . ')
                WHERE cr.active = 1 
                AND (cr.reduction_percent > 0 OR cr.reduction_amount > 0)
                AND (cr.date_to >= NOW() OR cr.date_to = "0000-00-00 00:00:00")
                ORDER BY cr.id_cart_rule DESC
                LIMIT ' . (int)$limit;

        $results = Db::getInstance()->executeS($sql);
        $clean = [];

        if ($results) {
            foreach ($results as $row) {
                $clean[] = [
                    'id_cart_rule' => $row['id_cart_rule'],
                    'rule_name' => $row['rule_name'],
                    'minimum_amount' => ($row['minimum_amount'] > 0) ? (int)$row['minimum_amount'] . ' uds' : '1 ud.',
                    'discount' => ($row['reduction_percent'] > 0) ? (float)$row['reduction_percent'] . '%' : $this->context->getCurrentLocale()->formatPrice($row['reduction_amount'], $this->context->currency->iso_code)
                ];
            }
        }
        return $clean;
    }
}