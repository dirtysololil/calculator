<?php

class MaterialCalcProductMaterialCreateProcessor extends modProcessor
{
    public $languageTopics = array('materialcalc:default');
    public $objectType = 'materialcalc.productmaterial';

    public function process()
    {
        $productId = (int)$this->getProperty('product_id');
        $materialId = (int)$this->getProperty('material_id');
        $amount = (float)$this->getProperty('amount', 1);
        $active = (int)$this->getProperty('active', 1);

        if ($productId <= 0) {
            return $this->failure('Укажите ID товара');
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

        $link = $this->modx->getObject('MaterialCalcProductMaterial', array(
            'product_id' => $productId,
            'material_id' => $materialId,
        ));

        if (!$link) {
            $link = $this->modx->newObject('MaterialCalcProductMaterial');
        }

        $link->fromArray(array(
            'product_id' => $productId,
            'material_id' => $materialId,
            'amount' => $amount,
            'active' => $active,
        ));

        if (!$link->save()) {
            return $this->failure('Не удалось сохранить привязку');
        }

        return $this->success('Привязка сохранена', $link->toArray());
    }
}

return 'MaterialCalcProductMaterialCreateProcessor';