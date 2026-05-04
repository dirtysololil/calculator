<?php
/**
 * Remove a material
 *
 * Note: Deleting a material will not automatically remove links to
 * products. You may wish to add your own logic in the future to
 * cascade or warn about orphaned product materials.
 *
 * @package materialcalc
 */
class MaterialCalcMaterialRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey       = 'MaterialCalcMaterial';
    public $objectType     = 'materialcalc.material';
    public $languageTopics = ['materialcalc:default'];
}

return 'MaterialCalcMaterialRemoveProcessor';