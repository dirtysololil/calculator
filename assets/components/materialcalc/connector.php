<?php
/**
 * MaterialCalc connector
 *
 * This file routes processor requests for the MaterialCalc component in
 * the MODX manager. It must be referenced by the JavaScript used in the
 * custom manager pages. Processors are located in
 * ``core/components/materialcalc/processors`` and are namespaced by
 * ``mgr`` for manager tasks.
 *
 * @package materialcalc
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

// Load the MaterialCalc package and register model
$corePath = $modx->getOption('materialcalc.core_path', null,
    $modx->getOption('core_path') . 'components/materialcalc/');
$modx->addPackage('materialcalc', $corePath . 'model/');

$processorsPath = $corePath . 'processors/';

$modx->request->handleRequest([
    'processors_path' => $processorsPath,
    'location'        => '',
]);