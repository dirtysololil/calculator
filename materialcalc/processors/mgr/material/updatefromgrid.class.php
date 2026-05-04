<?php

class MaterialCalcMaterialUpdateFromGridProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'MaterialCalcMaterial';
    public $objectType = 'materialcalc.material';
    public $languageTopics = array('materialcalc:default');
    public $primaryKeyField = 'id';

    public function initialize()
    {
        $data = $this->getProperty('data');

        if (!empty($data) && is_string($data)) {
            $data = $this->modx->fromJSON($data);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->setProperty($key, $value);
            }
            $this->unsetProperty('data');
        }

        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->modx->lexicon('materialcalc.material_err_ns');
        }

        $this->object = $this->modx->getObject($this->classKey, $id);

        if (!$this->object) {
            return $this->modx->lexicon('materialcalc.material_err_nf');
        }

        return true;
    }

    public function beforeSet()
    {
        $allowedFields = array(
            'name',
            'price',
            'weight',
            'weight_unit',
            'width',
            'length',
            'height',
            'depth',
            'size_unit',
            'color',
            'active',
            'rank',
        );

        $properties = $this->getProperties();

        foreach ($properties as $key => $value) {
            if ($key === 'id') {
                continue;
            }

            if (!in_array($key, $allowedFields, true)) {
                $this->unsetProperty($key);
            }
        }

        if ($this->getProperty('name') !== null) {
            $name = trim((string)$this->getProperty('name'));

            if ($name === '') {
                $this->addFieldError('name', $this->modx->lexicon('materialcalc.material_err_name'));
                return false;
            }

            $this->setProperty('name', $name);
        }

        if ($this->getProperty('price') !== null) {
            $this->setProperty('price', $this->normalizeNumber($this->getProperty('price')));
        }

        if ($this->getProperty('weight') !== null) {
            $this->setProperty('weight', $this->normalizeNumber($this->getProperty('weight')));
        }

        if ($this->getProperty('width') !== null) {
            $this->setProperty('width', $this->normalizeNumber($this->getProperty('width')));
        }

        if ($this->getProperty('length') !== null) {
            $this->setProperty('length', $this->normalizeNumber($this->getProperty('length')));
        }

        if ($this->getProperty('height') !== null) {
            $this->setProperty('height', $this->normalizeNumber($this->getProperty('height')));
        }

        if ($this->getProperty('depth') !== null) {
            $this->setProperty('depth', $this->normalizeNumber($this->getProperty('depth')));
        }

        if ($this->getProperty('rank') !== null) {
            $this->setProperty('rank', (int)$this->getProperty('rank'));
        }

        if ($this->getProperty('active') !== null) {
            $this->setProperty('active', (int)$this->getProperty('active') === 1 ? 1 : 0);
        }

        if ($this->getProperty('weight_unit') !== null) {
            $weightUnit = (string)$this->getProperty('weight_unit');
            $this->setProperty('weight_unit', $weightUnit === 'g' ? 'g' : 'kg');
        }

        if ($this->getProperty('size_unit') !== null) {
            $sizeUnit = (string)$this->getProperty('size_unit');
            $this->setProperty('size_unit', $sizeUnit === 'm' ? 'm' : 'mm');
        }

        if ($this->getProperty('color') !== null) {
            $this->setProperty('color', trim((string)$this->getProperty('color')));
        }

        return parent::beforeSet();
    }

    private function normalizeNumber($value)
    {
        $value = str_replace(',', '.', (string)$value);
        $value = (float)$value;

        return $value < 0 ? 0 : $value;
    }
}

return 'MaterialCalcMaterialUpdateFromGridProcessor';