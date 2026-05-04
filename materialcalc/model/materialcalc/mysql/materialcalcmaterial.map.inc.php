<?php
$xpdo_meta_map['MaterialCalcMaterial'] = array(
    'package' => 'materialcalc',
    'version' => '1.1',
    'table' => 'materialcalc_material',
    'extends' => 'xPDOSimpleObject',
    'fields' => array(
        'name' => '',
        'price' => 0.00,
        'unit' => '',
        'active' => 1,
        'rank' => 0,
    ),
    'fieldMeta' => array(
        'name' => array('dbtype' => 'varchar', 'precision' => '255', 'phptype' => 'string', 'null' => false, 'default' => ''),
        'price' => array('dbtype' => 'decimal', 'precision' => '12,2', 'phptype' => 'float', 'null' => false, 'default' => 0.00),
        'unit' => array('dbtype' => 'varchar', 'precision' => '50', 'phptype' => 'string', 'null' => false, 'default' => ''),
        'active' => array('dbtype' => 'tinyint', 'precision' => '1', 'phptype' => 'boolean', 'null' => false, 'default' => 1),
        'rank' => array('dbtype' => 'int', 'precision' => '10', 'phptype' => 'integer', 'null' => false, 'default' => 0),
        'weight' => array('dbtype' => 'decimal','precision' => '12,3','phptype' => 'float','null' => false,'default' => 0,),
        'weight_unit' => array('dbtype' => 'varchar','precision' => '10','phptype' => 'string','null' => false,'default' => 'kg',),
        'width' => array('dbtype' => 'decimal','precision' => '12,3','phptype' => 'float','null' => false,'default' => 0,),
        'length' => array('dbtype' => 'decimal','precision' => '12,3','phptype' => 'float','null' => false,'default' => 0,),
        'height' => array('dbtype' => 'decimal','precision' => '12,3','phptype' => 'float','null' => false,'default' => 0,),
        'depth' => array('dbtype' => 'decimal','precision' => '12,3','phptype' => 'float','null' => false,'default' => 0,),
        'size_unit' => array('dbtype' => 'varchar','precision' => '10','phptype' => 'string','null' => false,'default' => 'mm',),
        'color' => array('dbtype' => 'varchar','precision' => '191','phptype' => 'string','null' => false,'default' => '',),
    ),
);
/*By Dirtysolo*/
