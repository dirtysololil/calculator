<?php
if (!isset($modx) || !$modx instanceof modX) {
    return;
}

$corePath = $modx->getOption(
    'materialcalc.core_path',
    null,
    MODX_CORE_PATH . 'components/materialcalc/'
);

$modelPath = $corePath . 'model/';

if (!$modx->addPackage('materialcalc', $modelPath)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[MaterialCalc] Could not add package materialcalc: ' . $modelPath);
    return;
}

$classFile = $corePath . 'model/materialcalc/materialcalc.class.php';

if (!class_exists('MaterialCalc') && file_exists($classFile)) {
    require_once $classFile;
}

if (!class_exists('MaterialCalc')) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[MaterialCalc] Class MaterialCalc not found: ' . $classFile);
    return;
}

$materialCalc = new MaterialCalc($modx);

$getProductId = function ($product) use ($modx) {
    $productId = 0;

    if (is_object($product) && method_exists($product, 'get')) {
        $productId = (int)$product->get('id');

        if ($productId <= 0) {
            $productId = (int)$product->get('product_id');
        }
    }

    return $productId;
};

$applyPriceToProductObject = function ($product) use ($materialCalc, $getProductId, $modx) {
    if (!is_object($product) || !method_exists($product, 'set')) {
        return false;
    }

    $productId = $getProductId($product);

    if ($productId <= 0) {
        return false;
    }

    $price = $materialCalc->calculate($productId, true);

    if ($price === null) {
        return false;
    }

    $price = round((float)$price, 2);

    $product->set('price', $price);
    $product->set('new_price', $price);


    $product->set('cost', $price);

    $modx->log(modX::LOG_LEVEL_INFO, '[MaterialCalc] Product #' . $productId . ' price applied before cart: ' . $price);

    return true;
};

$recalculateCart = function ($cart) use ($materialCalc, $modx) {
    if (!is_object($cart) || !method_exists($cart, 'get') || !method_exists($cart, 'set')) {
        return false;
    }

    $items = $cart->get();

    if (!is_array($items) || empty($items)) {
        return false;
    }

    $changed = false;

    foreach ($items as $key => $item) {
        if (!is_array($item)) {
            continue;
        }

        $productId = 0;

        if (isset($item['id'])) {
            $productId = (int)$item['id'];
        } elseif (isset($item['product_id'])) {
            $productId = (int)$item['product_id'];
        }

        if ($productId <= 0) {
            continue;
        }

        $price = $materialCalc->calculate($productId);

        if ($price === null) {
            continue;
        }

        $price = round((float)$price, 2);
        $count = isset($item['count']) ? (float)$item['count'] : 1;

        $items[$key]['price'] = $price;
        $items[$key]['new_price'] = $price;
        $items[$key]['cost'] = round($price * $count, 2);

        $changed = true;

        $modx->log(modX::LOG_LEVEL_INFO, '[MaterialCalc] Cart item #' . $productId . ' recalculated: price=' . $price . ', count=' . $count . ', cost=' . $items[$key]['cost']);
    }

    if ($changed) {
        $cart->set($items);
    }

    return $changed;
};

$eventName = $modx->event->name;

switch ($eventName) {

    case 'OnLoadWebDocument':
        if (!$modx->resource || $modx->context->key === 'mgr') {
            break;
        }

        if ($modx->resource->get('class_key') !== 'msProduct') {
            break;
        }

        $productId = (int)$modx->resource->get('id');
        if ($productId <= 0) {
            break;
        }

        $price = $materialCalc->calculate($productId, true);
        if ($price === null) {
            break;
        }

        $price = round((float)$price, 2);

        // Подменяем цену на уровне ресурса ДО рендера страницы,
        // чтобы miniShop2/msOptionsPrice видели MaterialCalc-цену как базовую.
        $modx->resource->set('price', $price);
        $modx->resource->set('new_price', $price);
        $modx->setPlaceholder('price', number_format($price, 0, '.', ' '));
        $modx->setPlaceholder('price_raw', $price);

        break;

    case 'msOnGetProductPrice':
        $product = $modx->getOption('product', $scriptProperties);
        if ($product) {
            $productId = (int)$product->get('id');
            $calc = new MaterialCalc($modx);
            $price = $calc->calculate($productId, true);
            if ($price !== null) {
                $price = round((float)$price, 2);
                $product->set('price', $price);
                $product->set('new_price', $price);
                $product->set('cost', $price);
            }
        }
        break;

    case 'msOnBeforeAddToCart':
    case 'msOnBeforeChangeInCart':
    case 'msOnAddToCart':
    case 'msOnChangeInCart':
        $cart = $modx->getOption('cart', $scriptProperties, null);

        if (!$cart && isset($miniShop2) && is_object($miniShop2) && isset($miniShop2->cart)) {
            $cart = $miniShop2->cart;
        }

        if (!$cart && $modx->services && $modx->services->has('miniShop2')) {
            $miniShop2 = $modx->services->get('miniShop2');
            if ($miniShop2 && isset($miniShop2->cart)) {
                $cart = $miniShop2->cart;
            }
        }

        if ($cart) {
            $recalculateCart($cart);
        }

        break;
}

return;