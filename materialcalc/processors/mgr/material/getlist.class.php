<?php

class MaterialCalcMaterialGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'MaterialCalcMaterial';
    public $objectType = 'materialcalc.material';
    public $languageTopics = array('materialcalc:default');
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = trim((string)$this->getProperty('query', ''));

        if ($query !== '') {
            $where = array(
                'name:LIKE' => '%' . $query . '%',
                'OR:unit:LIKE' => '%' . $query . '%',
                'OR:color:LIKE' => '%' . $query . '%',
            );

            if (is_numeric($query)) {
                $where['OR:id:='] = (int)$query;
            }

            $c->where($where);
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        $array['id'] = isset($array['id']) ? (int)$array['id'] : 0;
        $array['name'] = isset($array['name']) ? (string)$array['name'] : '';
        $array['price'] = isset($array['price']) ? (float)$array['price'] : 0;
        $array['unit'] = isset($array['unit']) ? (string)$array['unit'] : 'шт';

        $array['weight'] = isset($array['weight']) ? (float)$array['weight'] : 0;
        $array['weight_unit'] = isset($array['weight_unit']) ? (string)$array['weight_unit'] : 'kg';

        $array['width'] = isset($array['width']) ? (float)$array['width'] : 0;
        $array['length'] = isset($array['length']) ? (float)$array['length'] : 0;
        $array['height'] = isset($array['height']) ? (float)$array['height'] : 0;
        $array['depth'] = isset($array['depth']) ? (float)$array['depth'] : 0;
        $array['size_unit'] = isset($array['size_unit']) ? (string)$array['size_unit'] : 'mm';

        $array['color'] = isset($array['color']) ? (string)$array['color'] : '';

        $array['active'] = isset($array['active']) ? (int)$array['active'] : 1;
        $array['rank'] = isset($array['rank']) ? (int)$array['rank'] : 0;

        return $array;
    }
}

return 'MaterialCalcMaterialGetListProcessor';