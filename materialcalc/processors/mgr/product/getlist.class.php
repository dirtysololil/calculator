<?php

class MaterialCalcProductGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $objectType = 'materialcalc.product';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'ASC';

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
        $query = trim((string)$this->getProperty('query', ''));

        $c->leftJoin(
            'msProductData',
            'Data',
            'Data.id = modResource.id'
        );

        $c->where(array(
            'modResource.class_key' => 'msProduct',
            'modResource.deleted' => 0,
        ));

        if ($query !== '') {
            $where = array(
                'modResource.pagetitle:LIKE' => '%' . $query . '%',
                'OR:modResource.longtitle:LIKE' => '%' . $query . '%',
                'OR:Data.article:LIKE' => '%' . $query . '%',
            );

            if (is_numeric($query)) {
                $where['OR:modResource.id:='] = (int)$query;
            }

            $c->where($where);
        }

        $c->select(array(
            'modResource.id',
            'modResource.pagetitle',
            'Data.article',
            'Data.price',
        ));

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        $id = isset($array['id']) ? (int)$array['id'] : 0;
        $pagetitle = isset($array['pagetitle']) ? (string)$array['pagetitle'] : '';
        $article = isset($array['article']) ? (string)$array['article'] : '';
        $price = isset($array['price']) ? $array['price'] : '';

        $label = '#' . $id;

        if ($pagetitle !== '') {
            $label .= ' — ' . $pagetitle;
        }

        if ($article !== '') {
            $label .= ' / Арт.: ' . $article;
        }

        return array(
            'id' => $id,
            'pagetitle' => $pagetitle,
            'article' => $article,
            'price' => $price,
            'label' => $label,
        );
    }
}

return 'MaterialCalcProductGetListProcessor';