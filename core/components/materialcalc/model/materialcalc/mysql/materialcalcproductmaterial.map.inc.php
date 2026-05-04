<?php
$xpdo_meta_map['MaterialCalcProductMaterial'] = array(
    'package' => 'materialcalc',
    'version' => '1.1',
    'table' => 'materialcalc_product_material',
    'extends' => 'xPDOSimpleObject',
    'fields' => array(
        'product_id' => 0,
        'material_id' => 0,
        'amount' => 1.000,
        'active' => 1,
    ),
    'fieldMeta' => array(
        'product_id' => array('dbtype' => 'int', 'precision' => '10', 'phptype' => 'integer', 'null' => false, 'default' => 0),
        'material_id' => array('dbtype' => 'int', 'precision' => '10', 'phptype' => 'integer', 'null' => false, 'default' => 0),
        'amount' => array('dbtype' => 'decimal', 'precision' => '12,3', 'phptype' => 'float', 'null' => false, 'default' => 1.000),
        'active' => array('dbtype' => 'tinyint', 'precision' => '1', 'phptype' => 'boolean', 'null' => false, 'default' => 1),
    ),
    'indexes' => array(
        'product' => array(
            'alias' => 'product', 'primary' => false, 'unique' => false, 'type' => 'BTREE',
            'columns' => array('product_id' => array('length' => '10', 'collation' => 'A', 'null' => false)),
        ),
        'material' => array(
            'alias' => 'material', 'primary' => false, 'unique' => false, 'type' => 'BTREE',
            'columns' => array('material_id' => array('length' => '10', 'collation' => 'A', 'null' => false)),
        ),
    ),
    'aggregates' => array(
        'Material' => array(
            'class' => 'MaterialCalcMaterial',
            'local' => 'material_id',
            'foreign' => 'id',
            'cardinality' => 'one',
            'owner' => 'foreign',
        ),
    ),
);
