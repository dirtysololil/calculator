<?php

class MaterialCalcSettingGetProcessor extends modProcessor
{
    public function process()
    {
        $key = trim((string)$this->getProperty('key', ''));

        if ($key === '') {
            return $this->failure('Не указан ключ настройки');
        }

        $defaultValue = '';

        if ($key === 'show_frontend') {
            $defaultValue = '0';
        }

        if (preg_match('/^product_markup_\d+$/', $key)) {
            $defaultValue = '1';
        }

        $setting = $this->modx->getObject('MaterialCalcSetting', array(
            'key' => $key,
        ));

        if (!$setting) {
            return $this->success('', array(
                'key' => $key,
                'value' => $defaultValue,
            ));
        }

        return $this->success('', array(
            'key' => $key,
            'value' => $setting->get('value'),
        ));
    }
}

return 'MaterialCalcSettingGetProcessor';