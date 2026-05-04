<?php
/**
 * MaterialCalc Manager Controller
 *
 * Defines the manager interface for the MaterialCalc component. This
 * controller loads custom JavaScript and CSS and renders the main
 * container where ExtJS will mount grids and forms. The actual UI
 * widgets are defined in ``assets/components/materialcalc/js/mgr/``.
 *
 * @package materialcalc
 */

class MaterialcalcHomeManagerController extends modExtraManagerController
{
    /** @var string The default sort order for menu items */
    public $defaultSortDirection = 'ASC';

    public function getPageTitle()
    {
        return $this->modx->lexicon('materialcalc');
    }

    public function loadCustomCssJs()
    {
        $assetsUrl   = $this->modx->getOption('materialcalc.assets_url', null,
            $this->modx->getOption('assets_url') . 'components/materialcalc/');
        $connectorUrl = $assetsUrl . 'connector.php';

        // Main JS entry point for the manager UI
        $this->addJavascript($assetsUrl . 'js/mgr/materialcalc.js');
        // Provide the connector URL to JS
        $this->addHtml('<script type="text/javascript">
        MaterialCalcConfig = {connectorUrl: "' . $connectorUrl . '"};
        </script>');
    }

    public function getTemplateFile()
    {
        return ''; // We will output HTML directly via process()
    }

    public function process(array $scriptProperties = [])
    {
        return '<div id="materialcalc-panel-home"></div>';
    }
}