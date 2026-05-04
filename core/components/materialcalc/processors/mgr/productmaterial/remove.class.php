<?php
/**
 * Remove a product-material link
 *
 * @package materialcalc
 */
class MaterialCalcProductMaterialRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey       = 'MaterialCalcProductMaterial';
    public $objectType     = 'materialcalc.productmaterial';
    public $languageTopics = ['materialcalc:default'];
}

return 'MaterialCalcProductMaterialRemoveProcessor';