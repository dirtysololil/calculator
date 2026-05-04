<?php

if (!defined('MODX_CORE_PATH')) {
    die('Access denied');
}

$modx->lexicon->load('materialcalc:default');

$assetsUrl = $modx->getOption(
    'materialcalc.assets_url',
    null,
    MODX_ASSETS_URL . 'components/materialcalc/'
);

$connectorUrl = $assetsUrl . 'connector.php';

$modx->regClientStartupHTMLBlock('
<script type="text/javascript">
    var MaterialCalcConfig = {
        connectorUrl: "' . $connectorUrl . '"
    };
</script>
');

$modx->regClientStartupScript($assetsUrl . 'js/mgr/materialcalc.js');

return '
<div class="container materialcalc-page">
    <h2 style="margin:0 0 15px 0;">Калькулятор материалов</h2>
    <div id="materialcalc-panel-home"></div>
</div>
';