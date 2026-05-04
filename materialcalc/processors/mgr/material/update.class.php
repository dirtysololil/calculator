<?php

class MaterialCalcMaterialUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'MaterialCalcMaterial';
    public $objectType = 'materialcalc.material';
    public $languageTopics = array('materialcalc:default');
    public $primaryKeyField = 'id';

    public function beforeSet()
    {
        $id = (int)$this->getProperty('id', 0);

        if ($id <= 0) {
            return 'Не передан ID материала';
        }

        $object = $this->modx->getObject($this->classKey, array(
            'id' => $id,
        ));

        if (!$object) {
            return 'Материал не найден';
        }

        /*
         * ВАЖНО:
         * При inline-редактировании MODX Grid может отправлять не все поля,
         * поэтому недостающие значения берём из текущего объекта.
         */
        $name = $this->getProperty('name', null);

        if ($name === null) {
            $name = $object->get('name');
        }

        $name = trim((string)$name);

        if ($name === '') {
            return 'Укажите название материала';
        }

        $this->setProperty('id', $id);
        $this->setProperty('name', $name);

        $this->setProperty(
            'price',
            $this->normalizeNumber(
                $this->getProperty('price', $object->get('price'))
            )
        );

        $this->setProperty('unit', '');

        $this->setProperty(
            'weight',
            $this->normalizeNumber(
                $this->getProperty('weight', $object->get('weight'))
            )
        );

        $this->setProperty(
            'weight_unit',
            $this->normalizeWeightUnit(
                $this->getProperty('weight_unit', $object->get('weight_unit'))
            )
        );

        $this->setProperty(
            'width',
            $this->normalizeNumber(
                $this->getProperty('width', $object->get('width'))
            )
        );

        $this->setProperty(
            'length',
            $this->normalizeNumber(
                $this->getProperty('length', $object->get('length'))
            )
        );

        $this->setProperty(
            'height',
            $this->normalizeNumber(
                $this->getProperty('height', $object->get('height'))
            )
        );

        $this->setProperty(
            'depth',
            $this->normalizeNumber(
                $this->getProperty('depth', $object->get('depth'))
            )
        );

        $this->setProperty(
            'size_unit',
            $this->normalizeSizeUnit(
                $this->getProperty('size_unit', $object->get('size_unit'))
            )
        );

        $color = $this->getProperty('color', null);

        if ($color === null) {
            $color = $object->get('color');
        }

        $this->setProperty('color', trim((string)$color));

        $active = $this->getProperty('active', null);

        if ($active === null) {
            $active = $object->get('active');
        }

        $this->setProperty('active', (int)$active === 1 ? 1 : 0);

        $rank = $this->getProperty('rank', null);

        if ($rank === null) {
            $rank = $object->get('rank');
        }

        $this->setProperty('rank', (int)$rank);

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

return 'MaterialCalcMaterialUpdateProcessor';