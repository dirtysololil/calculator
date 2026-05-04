{extends 'template:base_new'}
{block 'main'}
<div class="main-content">
    <section id="msProduct" data-ecommerce="detail">
        [[-<meta itemprop="identifier" content="{$_modx->resource.id}" />
        <meta itemprop="description" content="{$_modx->resource.description}">
        <meta itemprop="category" content="{$parent | resource: "pagetitle"}">]]
        <div class="container">
            <div class="result-page">
                <div class="page-zag">
                    <h1 [[-itemprop="name"]]>{$_modx->resource.pagetitle}</h1>
                </div>
                [[-<div class="pginfo">
                    <div class="pagination__total"><span>ORGPLEX</span></div>
                </div>]]
            </div>
            <div class="breadcrumbs">
                {'pdoCrumbs' | snippet : [
                    'showHome' => 1,
                    'outputSeparator' => '<span class="separator">-</span>',
                    'tplWrapper' => '@INLINE <ol id="breadcrumbs">{$output}</ol>',
                
                    'tpl' => '@INLINE
                        <li>
                            <a title="{$menutitle}" href="{$link}">
                                <span>{$menutitle}</span>
                            </a>
                        </li>
                    ',
                
                    'tplCurrent' => '@INLINE
                        <li class="current">
                            <span>{$pagetitle}</span>
                        </li>
                    '
                ]}
            </div>
            {if $_modx->resource.id == 2394}
            {else}
            [[-<div class="absolute-menu mt-2">
                {'pdoMenu' | snippet : [
                    'resources' => '8,5409,4046',
                    'parents' => 0,
                    'level' => 0,
                    'showHidden' => 1,
                    'tplOuter' => '@INLINE {$wrapper}',
                    'tpl' => '@INLINE <a target="_self" href="{$link}" {$classes} {$attributes}>{$menutitle}</a>{$wrapper}'
                ]}
            </div>]]
            {/if}
            <div class="page-nav mt-2">
                <div class="block-catalog">
                    <div class="product-side">
                        
                        {set $images = $_modx->resource['images'] | fromJSON}
                        <form class="ms2_form msoptionsprice-product product_info_side">
                            <div class="product_gallery">
                                {'!msGallery' | snippet : [
                                    'tpl' => 'custom-galery'
                                ]}
                            </div>
                            <div class="product_info_content" [[-itemscope itemtype="https://schema.org/Product"]]>
                                [[-<meta itemprop="name" content="[[*pagetitle]]">
                                <meta itemprop="description" content="{if $_modx->resource['introtext']}{$_modx->resource['introtext']}{else}{$_modx->resource['pagetitle']}{/if}">
                                <meta itemprop="sku" content="[[*id]]">
                                <meta itemprop="brand" content="Оргплекс">]]
                                
                                <div [[-itemprop="offers" itemscope itemtype="https://schema.org/Offer"]]>
                                    [[-<meta itemprop="price" content="{$price | replace:" ":""}">
                                    <meta itemprop="priceCurrency" content="RUB">
                                    <meta itemprop="priceValidUntil" content="2024-12-31">]]
                                    
                                    {if $_modx->resource['availability']}
                                        {if $_modx->resource['availability'] == 'В наличии'}
                                            <link [[-itemprop="availability"]] href="https://schema.org/InStock" />
                                        {else}
                                            <link [[-itemprop="availability"]] href="https://schema.org/OutOfStock" />
                                        {/if}
                                    {/if}
                                    
                                    [[-<div itemprop="shippingDetails" itemscope itemtype="https://schema.org/OfferShippingDetails">
                                        <div itemprop="shippingDestination" itemscope itemtype="https://schema.org/DefinedRegion">
                                            <meta itemprop="addressCountry" content="RU">
                                        </div>
                                        <div itemprop="shippingRate" itemscope itemtype="https://schema.org/MonetaryAmount">
                                            <meta itemprop="value" content="0">
                                            <meta itemprop="currency" content="RUB">
                                        </div>
                                        <div itemprop="deliveryTime" itemscope itemtype="https://schema.org/ShippingDeliveryTime">
                                            <div itemprop="handlingTime" itemscope itemtype="https://schema.org/QuantitativeValue">
                                                <meta itemprop="minValue" content="0">
                                                <meta itemprop="maxValue" content="1">
                                                <meta itemprop="unitCode" content="DAY">
                                            </div>
                                            <div itemprop="transitTime" itemscope itemtype="https://schema.org/QuantitativeValue">
                                                <meta itemprop="minValue" content="1">
                                                <meta itemprop="maxValue" content="5"> 
                                                <meta itemprop="unitCode" content="DAY">
                                            </div>
                                        </div>
                                    </div>]]
                                    
                                    [[-<div itemprop="hasMerchantReturnPolicy" itemscope itemtype="https://schema.org/MerchantReturnPolicy">
                                        <meta itemprop="applicableCountry" content="RU">
                                        <meta itemprop="returnPolicyCategory" content="https://schema.org/MerchantReturnFiniteReturnWindow">
                                        <meta itemprop="merchantReturnDays" content="14">
                                        <meta itemprop="returnMethod" content="https://schema.org/ReturnAtKiosk">
                                        <meta itemprop="returnFees" content="https://schema.org/FreeReturn">
                                    </div>]]
                                </div>
                                <!-- Конец блока offers -->
                                
                                <div class="options_product_index">
                                    <input type="hidden" name="id" value="[[*id]]"/>
                                    <input type="hidden" name="options[shelf_quantity]" id="shelf-quantity-hidden" value="[[!+shelf_quantity]]">
                                    
                                    <div class="product_data">
                                        <div class="product_data_h1">О товаре</div>
                                        <div class="product_data_charactik">
                                            Артикул: <span>[[*article:empty=`[[*id]]`]]</span>
                                        </div>
                                        <div class="manufacturer">
                                            Производитель: <span>Оргплекс</span>
                                        </div>
                                        {if $_modx->resource['availability']}
                                            <div class="avail">
                                                Наличие: 
                                                <span>{$_modx->resource['availability']}</span>
                                            </div>
                                        {/if}
                                        [[*parent:is=`2727`:then=` Минимальное количество 20 шт.`:else=``]]
                                    </div>
                                    {'!MaterialCalcProduct' | snippet : [
                                        'product_id' => $_modx->resource.id,
                                        'tpl' => 'tpl.MaterialCalcProductRow',
                                        'tplOuter' => 'tpl.MaterialCalcProductOuter',
                                        'pricePlaceholder' => 'materialcalc_price'
                                    ]}
                                    <div class="product-buy-grid">
                                        <div class="product_data_h1">Параметры:</div>
                                        <div class="product-oprions">
                                            {'!msOptions' | snippet : [
                                                'tpl' => 'msOptionsV2',
                                                'options' => 'color,czvet,size,tags,vyisota,glubina,shirina,nagruzka,veshalki,materia'
                                            ]}
                                            {'!msAddLinked.input' | snippet : [
                                                'product' => '[[+id]]',
                                                'tpl' => 'option_quantity',
                                                'input_type' => 'number',
                                                'toPlaceholder' => 'linked'
                                            ]}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pdoduct_info">
                                <div class="product-buys">
                                    {set $materialcalcPrice = $_modx->getPlaceholder('materialcalc_price')}
                                    <div class="product_price">
                                        {set $materialcalcExtraRaw = $_modx->getPlaceholder('materialcalc_extra_price_raw')|default:0}
                                        {set $materialcalcProductRaw = $_modx->getPlaceholder('materialcalc_product_price_raw')|default:($price|replace:' ':'')}
                                        <span class="product_base-price msoptionsprice-cost msoptionsprice-{$_modx->resource.id}" id="price" data-materialcalc-price="{$materialcalcPrice}" data-materialcalc-extra="{$materialcalcExtraRaw}" data-materialcalc-product-base="{$materialcalcProductRaw}">
                                            {if $materialcalcPrice}
                                                {$materialcalcPrice}
                                            {else}
                                                {$price}
                                            {/if}
                                        </span>
                                        <span class="product_base-price">₽</span>
                                        [[!msAddLinked.info? &option=`[[+option.msal]]`]]
                                        {if $_modx->resource['old_price']}
                                            <div class="old_price__prod">{$_modx->resource['old_price']}₽</div>
                                        {/if}
                                    </div>
                                    <div class="cheaper d-flex row">
                                        <img src="/assets/rinaf/assets/orgplex/img/cursor-click.svg" alt="Курсор" width="19" height="19" style="margin-right:10px">
                                        <div class="align-items-end"><a class="js-modal-show" data-target="#deshevle"data-target="#cheep">Нашли дешевле?</a>
                                        <p style="font-size:11px">Давайте обсудим цену</p></div>
                                    </div>
                                    [[*parent:is=`2727`:then=`
                                            <div class="number xdrx">
                                                <span class="minus">-</span>
                                                <input min="20" placeholder="minprice" type="number" name="count"  class="xds" placeholder="20 min"  value="20" id="product_price1" data-units="шт."/>
                                            	<span class="plus">+</span>
                                            </div>
                                        `:else=`
                                        <div class="ui-quantity"> 
                                            <button type="button" class="ui-quantity__btn ui-quantity__minus">
                                                <svg class="minus plus">
                                                    <use xlink:href="{'!getFileVersion' | snippet : [ 'path' => 'orgplex/img/symbol/sprite.svg#icons--ic-minus']}"></use>
                                                </svg>
                                            </button>
                                                <input type="text" name="count"   value="1 шт." id="product_price" data-units="шт.">
                                            <button type="button" class="ui-quantity__btn ui-quantity__plus">
                                                <svg class="plus">
                                                    <use xlink:href="{'!getFileVersion' | snippet : [ 'path' => 'orgplex/img/symbol/sprite.svg#icons--ic-plus']}"></use>
                                                </svg>
                                            </button>
                                        </div>`
                                    ]]
                                    <div class="product_buy">
                                        <button type="submit" class="btn_cart  btn btn-primary" name="ms2_action" value="cart/add">
                                            В корзину 
                                        </button>
                                        <div class="price_info">Данная цена указана розничная. При оптовом заказе, уточняйте цену у менеджера.</div>
                                    </div>
                                </div>
                                <div class="preim_zone">
                                    <div class="preim_zone_item">
                                        <svg class="ic3">
                                            <use xlink:href="assets/rinaf/assets/orgplex/img/symbol/sprite.svg#icons--3d"></use>
                                        </svg>
                                        <span>Изготовление пробных образцов</span>
                                    </div>
                                    <div class="preim_zone_item">
                                        <svg class="ic3">
                                            <use xlink:href="assets/rinaf/assets/orgplex/img/symbol/sprite.svg#icons--industrial-robot"></use>
                                        </svg>
                                        <span>Европейские технологии производства</span>
                                    </div>
                                </div>
                            </div>
                        <script>
                            (function () {
                                var priceNode = document.getElementById('price');
                                if (!priceNode) return;

                                var extra = parseFloat(String(priceNode.dataset.materialcalcExtra || '0').replace(',', '.')) || 0;
                                var baseFallback = parseFloat(String(priceNode.dataset.materialcalcProductBase || '0').replace(',', '.')) || 0;
                                var lock = false;

                                function parsePrice(text) {
                                    if (!text) return 0;
                                    var normalized = String(text).replace(/\s+/g, '').replace(/[^\d.,-]/g, '').replace(',', '.');
                                    var value = parseFloat(normalized);
                                    return isNaN(value) ? 0 : value;
                                }

                                function formatPrice(value) {
                                    return new Intl.NumberFormat('ru-RU', {maximumFractionDigits: 0}).format(value);
                                }

                                function applyMaterialExtra(basePrice) {
                                    var safeBase = (typeof basePrice === 'number' && !isNaN(basePrice)) ? basePrice : baseFallback;
                                    var total = Math.max(0, safeBase + extra);
                                    lock = true;
                                    priceNode.textContent = formatPrice(total);
                                    setTimeout(function () { lock = false; }, 0);
                                }

                                applyMaterialExtra(baseFallback);

                                var observer = new MutationObserver(function () {
                                    if (lock) return;
                                    var current = parsePrice(priceNode.textContent);
                                    applyMaterialExtra(current);
                                });

                                observer.observe(priceNode, {childList: true, subtree: true, characterData: true});

                                document.addEventListener('change', function (e) {
                                    if (e.target && e.target.name && e.target.name.indexOf('options[') === 0) {
                                        setTimeout(function () {
                                            applyMaterialExtra(parsePrice(priceNode.textContent));
                                        }, 30);
                                    }
                                });
                            })();
                        </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="custom-content">
                <h3>Характеристики</h3>
                <div class="tab-content">
                    <ul>
                        {set $chars = [
                          'garant' => 'Гарантия',
                          'srok' => 'Срок службы',
                          'material' => 'Материал',
                          'ves' => 'Вес'
                        ]}
                        {foreach $chars as $key =>  $row}
                        {if $_modx->resource[$key]}
                            <li class="dotted-item"><p>{$row} </p><span class="dotted-line"></span><span>{$_modx->resource[$key]}</span></li>
                        {/if}
                        {/foreach}
                        <li class="dotted-item"><p>Артикул </p><span class="dotted-line"></span><span>[[*article:empty=`[[*id]]`]]</span></li>
                        <li class="dotted-item"><p>Опросный лист </p><span class="dotted-line"></span><span><a href="/oprosnilist.pdf">Скачать</a></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="reviews sect_padding">
        <div class="container">
            <h3 class="pb-2 pt-5">Примеры работ</h3>
            <div class="swiper reviews_slider">
                <div class="swiper-wrapper">
                    [[+parent:product_portfolio_slider_snippet]]
                </div>
                <div class="navigation_slider">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
    </section>
    {if $_modx->resource['content']}
        <section>
            <div class="container">
                <div class="custom-content">
                    <h3>Описание</h3>
                    {$_modx->resource['content']}
                </div>
            </div>
        </section>
    {/if}
    [[-<section>
        <div class="container">
            <div class="custom-content">
                {'!product-recomendet' | snippet}
            </div>
        </div>
    </section>]]
    <section>
        <div class="container">
            <div class="catalog-left">
                <h3 class="pb-2 pt-5">Похожие товары</h3>
                <div class="catalog-group">
                    {'msProducts' | snippet : [
                        'parents' => $_modx->resource.parent,
                        'depth' => 1,
                        'includeTVs' => 'images,iii,img1',
                        'tvPrefix' => '',
                        'limit' => 4,
                        'tpl' => 'catalog-item',
                        'includeThumbs' => 'medium',
                    ]}
                </div>
            </div>
        </div>
    </section>
    {*include 'choose_z'*}
    <section class="main_form sect_padding">
        <div class="container">
            <div class="main_form_content">
                <div class="main_form_content_left">
                    <img loading="lazy" src="/orgplex/img/d1dec238c684e1b5d3a128307f07fde79a818c04.jpg" alt="orgplex">
                </div>
                {'!AjaxForm' | snippet : [
                    'snippet' => 'FormIt',
                    'hooks' => 'email,FormItAutoResponder',
                    'formFields' => 'tel,accept1,accept2',
                    'form' => 'specify_form',
                    'emailTpl' => 'slider_form_email',
                    'emailFrom' => '[[++emailsender]]',
                    'emailSubject' => 'Заявка на консультацию, страница - [[*pagetitle]]',
                    'emailTo' => 'info@orgplex.com,dirtysolo@vk.com',
                    'validationErrorMessage' => 'Ошибка отправки, попробуйте еще раз',
                    'successMessage' => 'Заявка отправлена.',
                    'validate' => 'tel:required,accept1:required,accept2:required',
                    'errTpl' => '<br /><span class="error" data-dd>[[+error]]</span>',
                ]}
            </div>
        </div>
    </section>
    <section class="map sect_padding">
        <div class="container">
            <div class="mpa_content">
                <div style="position:relative;overflow:hidden;">
                    <a href="https://yandex.ru/maps/org/orgplex/69169362105/?utm_medium=mapframe&utm_source=maps"
                    style="color:#eee;font-size:12px;position:absolute;top:0px;">Orgplex</a><a
                    href="https://yandex.ru/maps/21630/lytkarino/category/plexiglass_polycarbonate/41694184644/?utm_medium=mapframe&utm_source=maps"
                    style="color:#eee;font-size:12px;position:absolute;top:14px;">Оргстекло, поликарбонат в Лыткарине</a><a
                    href="https://yandex.ru/maps/21630/lytkarino/category/retail_equipment/184106990/?utm_medium=mapframe&utm_source=maps"
                    style="color:#eee;font-size:12px;position:absolute;top:28px;">Торговое оборудование в Лыткарине</a><iframe
                    src="https://yandex.ru/map-widget/v1/?ll=37.934013%2C55.559216&mode=search&oid=69169362105&ol=biz&source=serp_navig&z=17.13"
                    width="100%" height="400" frameborder="1" allowfullscreen="true" style="position:relative;" title="Как найти нашу компанию Orgplex"></iframe>
                </div>
            </div>
            <div class="main_cat_btn">
                <a href="{3 | url}" class="btn blue">Как добраться</a>
            </div>
        </div>
    </section>
</div>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "[[*pagetitle:htmlent]]",
  "description": "[[*description:stripTags:htmlent]]",
  "sku": "ORG-[[*id]]",
  "url": "[[~[[*id]]? &scheme=`full`]]",
  "image": [
    "[[++site_url]][[+image]]"
  ],
  "brand": {
    "@type": "Brand",
    "name": "Orgplex"
  },
  "offers": {
    "@type": "Offer",
    "url": "[[~[[*id]]? &scheme=`full`]]",
    "price": "[[*price]]",
    "priceCurrency": "RUB",
    "priceValidUntil": "2027-12-31",
    "availability": "https://schema.org/PreOrder",
    "itemCondition": "https://schema.org/NewCondition",
    "shippingDetails": {
      "@type": "OfferShippingDetails",
      "shippingDestination": {
        "@type": "DefinedRegion",
        "addressCountry": "RU"
      },
      "deliveryTime": {
        "@type": "ShippingDeliveryTime",
        "handlingTime": {
          "@type": "QuantitativeValue",
          "minValue": 3,
          "maxValue": 5,
          "unitCode": "DAY"
        }
      }
    },
    "hasMerchantReturnPolicy": {
      "@type": "MerchantReturnPolicy",
      "applicableCountry": "RU",
      "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
      "merchantReturnDays": 14,
      "returnMethod": "https://schema.org/ReturnByMail",
      "returnFees": "https://schema.org/ReturnFeesCustomerResponsibility"
    }
  }
}
</script>
{/block}