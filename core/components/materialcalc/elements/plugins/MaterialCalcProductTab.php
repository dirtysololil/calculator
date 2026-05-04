<?php
if ($modx->event->name !== 'OnDocFormPrerender') {
    return;
}

$id = isset($id) ? (int)$id : 0;

if ($id <= 0 && isset($scriptProperties['id'])) {
    $id = (int)$scriptProperties['id'];
}

if ($id <= 0) {
    return;
}

$resource = null;

if (isset($scriptProperties['resource']) && $scriptProperties['resource']) {
    $resource = $scriptProperties['resource'];
}

if (!$resource) {
    $resource = $modx->getObject('modResource', $id);
}

if (!$resource) {
    return;
}

if ($resource->get('class_key') !== 'msProduct') {
    return;
}

$assetsUrl = $modx->getOption(
    'materialcalc.assets_url',
    null,
    MODX_ASSETS_URL . 'components/materialcalc/'
);

$connectorUrl = $assetsUrl . 'connector.php';
$productTabJs = MODX_ASSETS_PATH . 'components/materialcalc/js/mgr/product-tab.js';
$productTabVersion = file_exists($productTabJs) ? filemtime($productTabJs) : time();

$modx->regClientStartupHTMLBlock(''
    . '<script type="text/javascript">' . PHP_EOL
    . 'var MaterialCalcConfig = window.MaterialCalcConfig || {};' . PHP_EOL
    . 'MaterialCalcConfig.connectorUrl = "' . $connectorUrl . '";' . PHP_EOL
    . '</script>'
);

$modx->regClientStartupScript($assetsUrl . 'js/mgr/product-tab.js?v=' . $productTabVersion);