<?php
/**
 * MaterialCalc service class.
 *
 * Цена товара = сумма себестоимости активных материалов × наценка товара.
 * Наценка товара хранится в MaterialCalcSetting с ключом product_markup_ID.
 */
class MaterialCalc
{
    /** @var modX */
    protected $modx;

    /** @var array */
    public $config = array();

    public function __construct(modX $modx, array $config = array())
    {
        $this->modx = $modx;

        $corePath = $this->modx->getOption(
            'materialcalc.core_path',
            $config,
            $this->modx->getOption('core_path') . 'components/materialcalc/'
        );
        $modelPath = $corePath . 'model/';

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'modelPath' => $modelPath,
            'modelNamespace' => 'materialcalc',
        ), $config);

        $this->modx->addPackage(
            $this->config['modelNamespace'],
            $this->config['modelPath']
        );
    }

    /**
     * @param int  $productId
     * @param bool $includeBasePrice
     * @return float|null
     */
    public function calculate($productId, $includeBasePrice = false)
    {
        $productId = (int)$productId;

        if ($productId <= 0) {
            return null;
        }

        $materialsTotal = $this->getMaterialsTotal($productId);

        if ($materialsTotal <= 0) {
            return null;
        }

        $price = $materialsTotal * $this->getProductMarkup($productId);

        if ($includeBasePrice) {
            $product = $this->modx->getObject('msProduct', $productId);

            if ($product) {
                $price += (float)$product->get('price');
            }
        }

        return round($price, 2);
    }

    /**
     * @param int $productId
     * @return float
     */
    public function getMaterialsTotal($productId)
    {
        $productId = (int)$productId;

        if ($productId <= 0) {
            return 0.0;
        }

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
            'materials_total' => 'SUM(Material.price * MaterialCalcProductMaterial.amount)',
        ));

        if ($q->prepare() && $q->stmt->execute()) {
            $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
            return isset($row['materials_total']) ? round((float)$row['materials_total'], 2) : 0.0;
        }

        return 0.0;
    }

    /**
     * @param int $productId
     * @return float
     */
    public function getProductMarkup($productId)
    {
        $productId = (int)$productId;

        if ($productId <= 0) {
            return 1.0;
        }

        $value = $this->getSetting('product_markup_' . $productId, 1);
        $value = str_replace(',', '.', (string)$value);
        $value = (float)$value;

        return $value > 0 ? $value : 1.0;
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getProductTotals($productId)
    {
        $productId = (int)$productId;
        $materialsTotal = $this->getMaterialsTotal($productId);
        $markup = $this->getProductMarkup($productId);

        return array(
            'cost_total' => round($materialsTotal, 2),
            'markup' => $markup,
            'total_with_markup' => round($materialsTotal * $markup, 2),
        );
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getSetting($key, $default = null)
    {
        $setting = $this->modx->getObject('MaterialCalcSetting', array(
            'key' => $key,
        ));

        if ($setting) {
            return $setting->get('value');
        }

        return $default;
    }
}
