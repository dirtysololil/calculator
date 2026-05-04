(function () {
  function toNumber(value) {
    if (value === null || value === undefined) return NaN;
    var normalized = String(value).replace(/\s+/g, '').replace(',', '.');
    var num = parseFloat(normalized);
    return isNaN(num) ? NaN : num;
  }

  function formatPrice(value) {
    var rounded = Math.round(value);
    return rounded.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  function initBridge() {
    var priceNode = document.getElementById('price');
    if (!priceNode) return;

    var ms2Base = toNumber(priceNode.getAttribute('data-ms2-price'));
    var materialBase = toNumber(priceNode.getAttribute('data-materialcalc-price-raw'));

    if (isNaN(ms2Base) || isNaN(materialBase)) return;

    function getCurrentMsopPrice() {
      var text = priceNode.textContent || priceNode.innerText || '';
      return toNumber(text);
    }

    function recalc() {
      var currentPrice = getCurrentMsopPrice();
      if (isNaN(currentPrice)) return;

      var difference = currentPrice - ms2Base;
      var finalPrice = materialBase + difference;

      priceNode.textContent = formatPrice(finalPrice);
      priceNode.setAttribute('data-materialcalc-price', formatPrice(finalPrice));
      priceNode.setAttribute('data-materialcalc-price-raw', String(Math.round(finalPrice)));
    }

    var form = document.querySelector('form.msoptionsprice-product');
    if (form) {
      form.addEventListener('change', function () {
        setTimeout(recalc, 0);
      });
      form.addEventListener('input', function () {
        setTimeout(recalc, 0);
      });
    }

    document.addEventListener('msoptionsprice_update', function () {
      setTimeout(recalc, 0);
    });

    recalc();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBridge);
  } else {
    initBridge();
  }
})();
