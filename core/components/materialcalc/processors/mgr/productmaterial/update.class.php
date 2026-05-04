<?php

class MaterialCalcProductMaterialUpdateProcessor extends modProcessor
{
    public $languageTopics = array('materialcalc:default');
    public $objectType = 'materialcalc.productmaterial';

    public function process()
    {
        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->failure('Не указан ID привязки');
        }

        /** @var MaterialCalcProductMaterial $object */
        $object = $this->modx->getObject('MaterialCalcProductMaterial', $id);

        if (!$object) {
            return $this->failure('Привязка не найдена');
        }

        $productId = (int)$this->getProperty('product_id', $object->get('product_id'));
        $materialId = (int)$this->getProperty('material_id', $object->get('material_id'));

        $amount = $this->getProperty('amount', $object->get('amount'));
        $amount = str_replace(',', '.', $amount);
        $amount = (float)$amount;

        $active = (int)$this->getProperty('active', $object->get('active'));

        if ($productId <= 0) {
            return $this->failure('Укажите товар');
        }

        if ($materialId <= 0) {
            return $this->failure('Выберите материал');
        }

        if ($amount <= 0) {
            return $this->failure('Расход должен быть больше нуля');
        }

        $product = $this->modx->getObject('modResource', $productId);

        if (!$product) {
            return $this->failure('Товар с ID ' . $productId . ' не найден');
        }

        if ($product->get('class_key') !== 'msProduct') {
            return $this->failure('Ресурс #' . $productId . ' не является товаром miniShop2');
        }

        $material = $this->modx->getObject('MaterialCalcMaterial', $materialId);

        if (!$material) {
            return $this->failure('Материал не найден');
        }

        $duplicate = $this->modx->getObject('MaterialCalcProductMaterial', array(
            'product_id' => $productId,
            'material_id' => $materialId,
        ));

        if ($duplicate && (int)$duplicate->get('id') !== $id) {
            return $this->failure('Этот материал уже привязан к выбранному товару');
        }

        $object->fromArray(array(
            'product_id' => $productId,
            'material_id' => $materialId,
            'amount' => $amount,
            'active' => $active ? 1 : 0,
        ));

        if (!$object->save()) {
            return $this->failure('Не удалось сохранить привязку');
        }

        return $this->success('Привязка сохранена', $object->toArray());
    }
}

return 'MaterialCalcProductMaterialUpdateProcessor';