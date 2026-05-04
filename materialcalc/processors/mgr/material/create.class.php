<?php

class MaterialCalcMaterialCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'MaterialCalcMaterial';
    public $objectType = 'materialcalc.material';
    public $languageTopics = array('materialcalc:default');

    public function beforeSet()
    {
        $name = trim((string)$this->getProperty('name', ''));

        if ($name === '') {
            return 'Укажите название материала';
        }

        $this->setProperty('name', $name);
        $this->setProperty('price', $this->normalizeNumber($this->getProperty('price', 0)));
        $this->setProperty('unit', '');

        $this->setProperty('weight', $this->normalizeNumber($this->getProperty('weight', 0)));
        $this->setProperty('weight_unit', $this->normalizeWeightUnit($this->getProperty('weight_unit', 'kg')));

        $this->setProperty('width', $this->normalizeNumber($this->getProperty('width', 0)));
        $this->setProperty('length', $this->normalizeNumber($this->getProperty('length', 0)));
        $this->setProperty('height', $this->normalizeNumber($this->getProperty('height', 0)));
        $this->setProperty('depth', $this->normalizeNumber($this->getProperty('depth', 0)));
        $this->setProperty('size_unit', $this->normalizeSizeUnit($this->getProperty('size_unit', 'mm')));

        $this->setProperty('color', trim((string)$this->getProperty('color', '')));
        $this->setProperty('active', (int)$this->getProperty('active', 1) === 1 ? 1 : 0);
        $this->setProperty('rank', (int)$this->getProperty('rank', 0));

        return parent::beforeSet();
    }

    protected function normalizeNumber($value)
    {
        $value = str_replace(',', '.', (string)$value);
        $value = (float)$value;

        return $value < 0 ? 0 : $value;
    }

    protected function normalizeWeightUnit($value)
    {
        $value = (string)$value;

        return $value === 'g' ? 'g' : 'kg';
    }

    protected function normalizeSizeUnit($value)
    {
        $value = (string)$value;

        return $value === 'm' ? 'm' : 'mm';
    }
}

return 'MaterialCalcMaterialCreateProcessor';