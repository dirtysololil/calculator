<?php

class MaterialCalcSettingUpdateProcessor extends modProcessor
{
    public function process()
    {
        $key = trim((string)$this->getProperty('key', ''));
        $value = (string)$this->getProperty('value', '');

        if ($key === '') {
            return $this->failure('Не указан ключ настройки');
        }

        $isProductMarkup = preg_match('/^product_markup_\d+$/', $key) === 1;

        $allowedKeys = array(
            'show_frontend',
        );

        if (!$isProductMarkup && !in_array($key, $allowedKeys, true)) {
            return $this->failure('Недопустимая настройка');
        }

        if ($key === 'show_frontend') {
            $value = (int)$value === 1 ? 1 : 0;
        }

        if ($isProductMarkup) {
            $value = str_replace(',', '.', $value);
            $value = (float)$value;

            if ($value <= 0) {
                $value = 1;
            }
        }

        $setting = $this->modx->getObject('MaterialCalcSetting', array(
            'key' => $key,
        ));

        if (!$setting) {
            $setting = $this->modx->newObject('MaterialCalcSetting');
            $setting->set('key', $key);
        }

        $setting->set('value', (string)$value);

        if (!$setting->save()) {
            return $this->failure('Не удалось сохранить настройку');
        }

        return $this->success('Настройка сохранена', array(
            'key' => $key,
            'value' => $value,
        ));
    }
}

return 'MaterialCalcSettingUpdateProcessor';