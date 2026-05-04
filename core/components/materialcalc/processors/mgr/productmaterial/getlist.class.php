<?php

class MaterialCalcProductMaterialGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'MaterialCalcProductMaterial';
    public $objectType = 'materialcalc.productmaterial';
    public $languageTopics = array('materialcalc:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    public function initialize()
    {
        $miniShop2CorePath = $this->modx->getOption(
            'minishop2.core_path',
            null,
            MODX_CORE_PATH . 'components/minishop2/'
        );

        $this->modx->addPackage('minishop2', $miniShop2CorePath . 'model/');

        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = trim($this->getProperty('query', ''));
        $productId = (int)$this->getProperty('product_id', 0);

        $c->leftJoin(
            'MaterialCalcMaterial',
            'Material',
            'Material.id = MaterialCalcProductMaterial.material_id'
        );

        $c->leftJoin(
            'modResource',
            'Product',
            'Product.id = MaterialCalcProductMaterial.product_id'
        );

        $c->leftJoin(
            'msProductData',
            'Data',
            'Data.id = MaterialCalcProductMaterial.product_id'
        );

        $c->select($this->modx->getSelectColumns(
            'MaterialCalcProductMaterial',
            'MaterialCalcProductMaterial'
        ));

        $c->select(array(
            'Material.name AS material_name',
            'Material.price AS material_price',
            'Material.color AS material_color',
            'Product.pagetitle AS product_name',
            'Data.article AS product_article',
            'Data.price AS product_price',
        ));

        if ($productId > 0) {
            $c->where(array(
                'MaterialCalcProductMaterial.product_id' => $productId,
            ));
        }

        if ($query !== '') {
            $where = array(
                'Material.name:LIKE' => '%' . $query . '%',
                'OR:Product.pagetitle:LIKE' => '%' . $query . '%',
                'OR:Data.article:LIKE' => '%' . $query . '%',
            );

            if (is_numeric($query)) {
                $where['OR:MaterialCalcProductMaterial.product_id:='] = (int)$query;
                $where['OR:MaterialCalcProductMaterial.material_id:='] = (int)$query;
            }

            $c->where($where);
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        $price = isset($array['material_price']) ? (float)$array['material_price'] : 0;
        $amount = isset($array['amount']) ? (float)$array['amount'] : 0;
        $productId = isset($array['product_id']) ? (int)$array['product_id'] : 0;

        $array['row_sum'] = round($price * $amount, 2);
        $array['product_markup'] = $this->getProductMarkup($productId);

        return $array;
    }

    public function outputArray(array $array, $count = false)
    {
        $productId = (int)$this->getProperty('product_id', 0);

        $output = array(
            'success' => true,
            'total' => (int)$count,
            'results' => $array,
        );

        if ($productId > 0) {
            $output['totals'] = $this->getProductTotals($productId);
        } else {
            $costTotal = 0;

            foreach ($array as $row) {
                if (isset($row['row_sum'])) {
                    $costTotal += (float)$row['row_sum'];
                }
            }

            $output['totals'] = array(
                'cost_total' => round($costTotal, 2),
                'markup' => 1,
                'total_with_markup' => round($costTotal, 2),
            );
        }

        return $this->modx->toJSON($output);
    }

    protected function getProductMarkup($productId)
    {
        $productId = (int)$productId;

        if ($productId <= 0) {
            return 1;
        }

        $setting = $this->modx->getObject('MaterialCalcSetting', array(
            'key' => 'product_markup_' . $productId,
        ));

        if (!$setting) {
            return 1;
        }

        $value = str_replace(',', '.', (string)$setting->get('value'));
        $value = (float)$value;

        return $value > 0 ? $value : 1;
    }

    protected function getProductTotals($productId)
    {
        $productId = (int)$productId;
        $markup = $this->getProductMarkup($productId);
        $costTotal = 0;

        if ($productId > 0) {
            $q = $this->modx->newQuery('MaterialCalcProductMaterial');
            $q->leftJoin(
                'MaterialCalcMaterial',
                'Material',
                'Material.id = MaterialCalcProductMaterial.material_id'
            );
            $q->where(array(
                'MaterialCalcProductMaterial.product_id' => $productId,
                'MaterialCalcProductMaterial.active' => 1,
                'Material.active' => 1,
            ));
            $q->select(array(
                'cost_total' => 'SUM(Material.price * MaterialCalcProductMaterial.amount)',
            ));

            if ($q->prepare() && $q->stmt->execute()) {
                $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
                $costTotal = isset($row['cost_total']) ? (float)$row['cost_total'] : 0;
            }
        }

        $costTotal = round($costTotal, 2);

        return array(
            'cost_total' => $costTotal,
            'markup' => $markup,
            'total_with_markup' => round($costTotal * $markup, 2),
        );
    }
}

return 'MaterialCalcProductMaterialGetListProcessor';
