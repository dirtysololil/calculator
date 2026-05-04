<?php
$productId = (int)$modx->getOption('product_id', $scriptProperties, 0);
$tpl = (string)$modx->getOption('tpl', $scriptProperties, 'tpl.MaterialCalcProductRow');
$tplOuter = (string)$modx->getOption('tplOuter', $scriptProperties, 'tpl.MaterialCalcProductOuter');
$pricePlaceholder = (string)$modx->getOption('pricePlaceholder', $scriptProperties, 'materialcalc_price');

if ($productId <= 0 && $modx->resource) {
    $productId = (int)$modx->resource->get('id');
}

if ($productId <= 0) {
    return '';
}

$corePath = $modx->getOption(
    'materialcalc.core_path',
    null,
    $modx->getOption('core_path') . 'components/materialcalc/'
);

$modelPath = $corePath . 'model/';

if (!$modx->addPackage('materialcalc', $modelPath)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[MaterialCalcProduct] Не удалось загрузить package materialcalc');
    return '';
}

$showFrontend = 0;

$showSetting = $modx->getObject('MaterialCalcSetting', array(
    'key' => 'show_frontend',
));

if ($showSetting) {
    $showFrontend = (int)$showSetting->get('value');
}

$markup = 1;

$markupSetting = $modx->getObject('MaterialCalcSetting', [
    'key' => 'product_markup_' . $productId,
]);

if (!$markupSetting || !(float)$markupSetting->get('value') > 0) {
    $markupSetting = $modx->getObject('MaterialCalcSetting', ['key' => 'markup_percent']);
}

if ($markupSetting) {
    $markupValue = str_replace(',', '.', (string)$markupSetting->get('value'));
    $markupValue = (float)$markupValue;
    if ($markupValue > 0) {
        $markup = $markupValue;
    }
}

$productPrice = 0;

$product = $modx->getObject('msProduct', $productId);

if ($product) {
    $productPrice = (float)$product->get('price');
}

$q = $modx->newQuery('MaterialCalcProductMaterial');

$q->leftJoin(
    'MaterialCalcMaterial',
    'Material',
    'Material.id = MaterialCalcProductMaterial.material_id'
);

$q->where(array(
    'MaterialCalcProductMaterial.product_id' => $productId,
    'MaterialCalcProductMaterial.active' => 1,
    'Material.active' => 1,
));

$q->select(array(
    'MaterialCalcProductMaterial.id',
    'MaterialCalcProductMaterial.amount',
    'Material.name AS material_name',
    'Material.price AS material_price',
));

$q->sortby('MaterialCalcProductMaterial.id', 'ASC');

$rows = array();
$totalMaterials = 0;

if ($q->prepare() && $q->stmt->execute()) {
    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
        $amount = isset($row['amount']) ? (float)$row['amount'] : 0;
        $price = isset($row['material_price']) ? (float)$row['material_price'] : 0;
        $sum = $amount * $price;

        $totalMaterials += $sum;

        $rows[] = array(
            'id' => isset($row['id']) ? (int)$row['id'] : 0,
            'name' => isset($row['material_name']) ? (string)$row['material_name'] : '',
            'price' => $price,
            'price_formatted' => number_format($price, 0, '.', ' '),
            'amount' => $amount,
            'amount_formatted' => rtrim(rtrim(number_format($amount, 3, '.', ' '), '0'), '.'),
            'sum' => $sum,
            'sum_formatted' => number_format($sum, 0, '.', ' '),

            'unit' => '',
            'weight' => '',
            'weight_raw' => 0,
            'weight_unit' => '',
            'weight_unit_label' => '',
            'width' => '',
            'width_raw' => 0,
            'length' => '',
            'length_raw' => 0,
            'height' => '',
            'height_raw' => 0,
            'depth' => '',
            'depth_raw' => 0,
            'size_unit' => '',
            'size_unit_label' => '',
            'color' => '',
        );
    }
}

if (empty($rows)) {
    return '';
}

$materialsWithMarkup = $totalMaterials * $markup;
$totalPrice          = $productPrice + $materialsWithMarkup;

$modx->setPlaceholder($pricePlaceholder, number_format($totalPrice, 0, '.', ' '));
$modx->setPlaceholder('materialcalc_price_raw', $totalPrice);

$modx->setPlaceholder('materialcalc_extra_price', number_format($materialsWithMarkup, 0, '.', ' '));
$modx->setPlaceholder('materialcalc_extra_price_raw', $materialsWithMarkup);

$modx->setPlaceholder('materialcalc_product_price', number_format($productPrice, 0, '.', ' '));
$modx->setPlaceholder('materialcalc_product_price_raw', $productPrice);

$modx->setPlaceholder('materialcalc_materials_total', number_format($totalMaterials, 0, '.', ' '));
$modx->setPlaceholder('materialcalc_materials_total_raw', $totalMaterials);

$modx->setPlaceholder('materialcalc_markup', $markup);
$modx->setPlaceholder('materialcalc_markup_percent', '');

if ($showFrontend !== 1) {
    return '';
}

$output = '';

foreach ($rows as $row) {
    $output .= $modx->getChunk($tpl, $row);
}

$markupHtml = '';

if ($markup > 0 && $markup != 1) {
    $markupHtml = '<div class="materialcalc-summary__row">
        <span>Наценка</span>
        <b>× ' . rtrim(rtrim(number_format($markup, 3, '.', ' '), '0'), '.') . '</b>
    </div>';
}

return $modx->getChunk($tplOuter, array(
    'rows' => $output,
    'product_price' => number_format($productPrice, 0, '.', ' '),
    'materials_total' => number_format($totalMaterials, 0, '.', ' '),
    'materials_with_markup' => number_format($materialsWithMarkup, 0, '.', ' '),
    'markup' => rtrim(rtrim(number_format($markup, 3, '.', ' '), '0'), '.'),
    'markup_percent' => '',
    'markup_html' => $markupHtml,
    'total_price' => number_format($totalPrice, 0, '.', ' '),
));