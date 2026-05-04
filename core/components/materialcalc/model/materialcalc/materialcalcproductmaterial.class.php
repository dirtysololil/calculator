<?php
class MaterialCalcProductMaterial extends xPDOSimpleObject
{
    public function getMaterial()
    {
        return $this->getOne('Material');
    }
}
