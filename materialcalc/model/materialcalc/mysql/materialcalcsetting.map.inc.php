<?php
$xpdo_meta_map['MaterialCalcSetting'] = array(
    'package' => 'materialcalc',
    'version' => '1.1',
    'table' => 'materialcalc_setting',
    'extends' => 'xPDOSimpleObject',
    'fields' => array(
        'key' => '',
        'value' => '',
    ),
    'fieldMeta' => array(
        'key' => array('dbtype' => 'varchar', 'precision' => '100', 'phptype' => 'string', 'null' => false, 'default' => ''),
        'value' => array('dbtype' => 'varchar', 'precision' => '255', 'phptype' => 'string', 'null' => false, 'default' => ''),
    ),
    'indexes' => array(
        'key' => array(
            'alias' => 'key',
            'primary' => false,
            'unique' => true,
            'type' => 'BTREE',
            'columns' => array(
                'key' => array('length' => '100', 'collation' => 'A', 'null' => false),
            ),
        ),
    ),
);
