Ext.namespace(
  "MaterialCalc",
  "MaterialCalc.grid",
  "MaterialCalc.window",
  "MaterialCalc.combo",
  "MaterialCalc.utils"
);

MaterialCalc.utils = MaterialCalc.utils || {};

MaterialCalc.utils.makeNumber = function (value, fallback) {
  if (value === null || typeof value === "undefined" || value === "") {
    return typeof fallback === "undefined" ? 0 : fallback;
  }

  value = String(value).replace(",", ".");
  value = parseFloat(value);

  return isNaN(value) ? (typeof fallback === "undefined" ? 0 : fallback) : value;
};

MaterialCalc.utils.formatNumber = function (value, decimals) {
  decimals = typeof decimals === "undefined" ? 2 : decimals;
  value = MaterialCalc.utils.makeNumber(value, 0);
  return value.toFixed(decimals);
};

MaterialCalc.getCurrentProductId = function () {
  var id = 0;

  if (typeof MODx !== "undefined" && MODx.request && MODx.request.id) {
    id = parseInt(MODx.request.id, 10);
  }

  if (!id && typeof MODx !== "undefined" && MODx.request && MODx.request.a) {
    id = parseInt(MODx.request.id || 0, 10);
  }

  return id || 0;
};

MaterialCalc.getProductMarkupKey = function (productId) {
  productId = parseInt(productId || 0, 10);
  return "product_markup_" + productId;
};

MaterialCalc.combo.Material = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    name: config.name || "material_id",
    hiddenName: config.hiddenName || "material_id",
    displayField: "name",
    valueField: "id",
    fields: ["id", "name", "price"],
    url: MaterialCalcConfig.connectorUrl,
    baseParams: {
      action: "mgr/material/getlist",
      combo: 1,
    },
    queryParam: "query",
    pageSize: 20,
    typeAhead: false,
    editable: true,
    minChars: 1,
    forceSelection: true,
    triggerAction: "all",
    mode: "remote",
    allowBlank: false,
    emptyText: "Выберите материал из справочника",
    tpl: new Ext.XTemplate(
      '<tpl for=".">',
      '<div class="x-combo-list-item">',
      "<b>{name}</b>",
      '<tpl if="price">',
      '<br><span style="color:#777;">Цена м2/шт: {price}</span>',
      "</tpl>",
      "</div>",
      "</tpl>"
    ),
    listeners: {
      select: function (combo, record) {
        combo.setValue(record.get("id"));
        combo.setRawValue(record.get("name"));
      },
    },
  });

  MaterialCalc.combo.Material.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.combo.Material, MODx.combo.ComboBox);
Ext.reg("materialcalc-combo-material", MaterialCalc.combo.Material);

MaterialCalc.grid.ProductTabMaterials = function (config) {
  config = config || {};

  var productId = config.product_id || MaterialCalc.getCurrentProductId();

  this.productId = productId;
  this.markupFieldId = "materialcalc-product-markup-field-" + productId;
  this.summaryBlockId = "materialcalc-product-summary-" + productId;
  this.storeLoadBound = false;

  Ext.applyIf(config, {
    id: "materialcalc-grid-product-tab-materials",
    url: MaterialCalcConfig.connectorUrl,
    baseParams: {
      action: "mgr/productmaterial/getlist",
      product_id: productId,
    },
    fields: [
      "id",
      "product_id",
      "material_id",
      "material_name",
      "material_price",
      "amount",
      "row_sum",
      "active",
      "product_markup",
    ],
    paging: true,
    remoteSort: true,
    preventRender: true,
    autoHeight: false,
    height: 330,
    anchor: "100%",
    save_action: "mgr/productmaterial/update",
    autosave: true,
    viewConfig: {
      forceFit: true,
      emptyText: "К этому товару материалы ещё не привязаны",
    },
    columns: [
      {
        header: "ID",
        dataIndex: "id",
        width: 50,
        sortable: true,
      },
      {
        header: "Материал",
        dataIndex: "material_name",
        width: 300,
        sortable: true,
      },
      {
        header: "Цена м2/шт",
        dataIndex: "material_price",
        width: 120,
        sortable: true,
        renderer: function (value) {
          return MaterialCalc.utils.formatNumber(value, 2);
        },
      },
      {
        header: "Расход",
        dataIndex: "amount",
        width: 100,
        sortable: true,
        editor: {
          xtype: "numberfield",
          decimalPrecision: 3,
          allowNegative: false,
          allowBlank: false,
        },
      },
      {
        header: "Себестоимость",
        dataIndex: "row_sum",
        width: 130,
        sortable: false,
        renderer: function (value, cell, row) {
          var price = MaterialCalc.utils.makeNumber(row.data.material_price, 0);
          var amount = MaterialCalc.utils.makeNumber(row.data.amount, 0);
          var sum = price * amount;

          if (!sum) {
            sum = MaterialCalc.utils.makeNumber(value, 0);
          }

          return MaterialCalc.utils.formatNumber(sum, 2);
        },
      },
      {
        header: "Активен",
        dataIndex: "active",
        width: 80,
        sortable: true,
        renderer: function (value) {
          return Number(value) === 1 || value === true ? "Да" : "Нет";
        },
        editor: {
          xtype: "combo-boolean",
        },
      },
    ],
    tbar: [
      {
        text: "Добавить материал",
        handler: this.createMaterialLink,
        scope: this,
      },
      "-",
      {
        xtype: "displayfield",
        value: '<span style="display:inline-block;padding:4px 4px 0 4px;font-weight:bold;">Наценка:</span>',
      },
      {
        xtype: "numberfield",
        id: this.markupFieldId,
        width: 80,
        decimalPrecision: 3,
        allowNegative: false,
        allowBlank: false,
        value: 1,
        listeners: {
          specialkey: {
            fn: function (field, e) {
              if (e.getKey() === e.ENTER) {
                this.saveProductMarkup();
              }
            },
            scope: this,
          },
        },
      },
      {
        text: "Применить",
        handler: this.saveProductMarkup,
        scope: this,
      },
      "->",
      {
        text: "Обновить",
        handler: function () {
          this.refresh();
        },
        scope: this,
      },
    ],
    listeners: {
      afteredit: {
        fn: function () {
          this.refresh.defer(300, this);
        },
        scope: this,
      },
      afterrender: {
        fn: function () {
          this.initSummaryBlock();
          this.bindStoreLoad();
          this.loadProductMarkup();

          var store = this.getStore();
          var json = store && store.reader && store.reader.jsonData ? store.reader.jsonData : null;

          if (json && json.totals) {
            this.updateSummaryBlock(json.totals);
          } else {
            this.refresh.defer(200, this);
          }
        },
        scope: this,
      },
    },
  });

  MaterialCalc.grid.ProductTabMaterials.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.grid.ProductTabMaterials, MODx.grid.Grid, {
  bindStoreLoad: function () {
    if (this.storeLoadBound) {
      return;
    }

    var store = this.getStore();

    if (!store) {
      return;
    }

    store.on(
      "load",
      function (store) {
        var json = store.reader && store.reader.jsonData ? store.reader.jsonData : null;

        if (json && json.totals) {
          this.updateSummaryBlock(json.totals);
        } else {
          this.updateSummaryBlock(this.calculateTotalsFromStore());
        }
      },
      this
    );

    this.storeLoadBound = true;
  },

  getMarkupField: function () {
    return Ext.getCmp(this.markupFieldId);
  },

  initSummaryBlock: function () {
    if (Ext.get(this.summaryBlockId) || !this.getEl()) {
      return;
    }

    Ext.DomHelper.insertAfter(this.getEl(), {
      tag: "div",
      id: this.summaryBlockId,
      style:
        "display:flex;justify-content:flex-end;gap:16px;align-items:stretch;" +
        "padding:14px 16px;margin:0 0 10px 0;background:#f7f9fb;" +
        "border:1px solid #dce3ea;border-top:0;border-radius:0 0 4px 4px;",
      html:
        '<div style="min-width:160px;padding:10px 14px;background:#fff;border:1px solid #e1e6eb;border-radius:4px;">' +
        '<div style="color:#777;font-size:12px;margin-bottom:4px;">Себестоимость</div>' +
        '<div id="' + this.summaryBlockId + '-cost" style="font-size:18px;font-weight:bold;">0.00</div>' +
        "</div>" +
        '<div style="min-width:120px;padding:10px 14px;background:#fff;border:1px solid #e1e6eb;border-radius:4px;">' +
        '<div style="color:#777;font-size:12px;margin-bottom:4px;">Наценка</div>' +
        '<div id="' + this.summaryBlockId + '-markup" style="font-size:18px;font-weight:bold;">1.000</div>' +
        "</div>" +
        '<div style="min-width:160px;padding:10px 14px;background:#fff;border:1px solid #e1e6eb;border-radius:4px;">' +
        '<div style="color:#777;font-size:12px;margin-bottom:4px;">Итого</div>' +
        '<div id="' + this.summaryBlockId + '-total" style="font-size:18px;font-weight:bold;">0.00</div>' +
        "</div>",
    });
  },

  calculateTotalsFromStore: function () {
    var field = this.getMarkupField();
    var markup = field ? MaterialCalc.utils.makeNumber(field.getValue(), 1) : 1;
    var costTotal = 0;

    if (markup <= 0) {
      markup = 1;
    }

    if (this.getStore()) {
      this.getStore().each(function (record) {
        var active = record.get("active");

        if (!(Number(active) === 1 || active === true)) {
          return;
        }

        var price = MaterialCalc.utils.makeNumber(record.get("material_price"), 0);
        var amount = MaterialCalc.utils.makeNumber(record.get("amount"), 0);

        costTotal += price * amount;
      });
    }

    return {
      cost_total: costTotal,
      markup: markup,
      total_with_markup: costTotal * markup,
    };
  },

  updateSummaryBlock: function (serverTotals) {
    var field = this.getMarkupField();
    var markup = field ? MaterialCalc.utils.makeNumber(field.getValue(), 1) : 1;
    var costTotal = 0;
    var totalWithMarkup = 0;

    if (serverTotals) {
      costTotal = MaterialCalc.utils.makeNumber(serverTotals.cost_total, 0);
      markup = MaterialCalc.utils.makeNumber(serverTotals.markup, markup);
      totalWithMarkup = MaterialCalc.utils.makeNumber(
        serverTotals.total_with_markup,
        costTotal * markup
      );

      if (field && markup > 0) {
        field.setValue(markup);
      }
    } else {
      serverTotals = this.calculateTotalsFromStore();
      costTotal = MaterialCalc.utils.makeNumber(serverTotals.cost_total, 0);
      markup = MaterialCalc.utils.makeNumber(serverTotals.markup, markup);
      totalWithMarkup = MaterialCalc.utils.makeNumber(
        serverTotals.total_with_markup,
        costTotal * markup
      );
    }

    if (markup <= 0) {
      markup = 1;
    }

    var costEl = Ext.get(this.summaryBlockId + "-cost");
    var markupEl = Ext.get(this.summaryBlockId + "-markup");
    var totalEl = Ext.get(this.summaryBlockId + "-total");

    if (costEl) {
      costEl.update(MaterialCalc.utils.formatNumber(costTotal, 2));
    }

    if (markupEl) {
      markupEl.update(MaterialCalc.utils.formatNumber(markup, 3));
    }

    if (totalEl) {
      totalEl.update(MaterialCalc.utils.formatNumber(totalWithMarkup, 2));
    }
  },

  loadProductMarkup: function () {
    var productId = this.productId || MaterialCalc.getCurrentProductId();
    var field = this.getMarkupField();

    if (!productId || !field) {
      return;
    }

    MODx.Ajax.request({
      url: MaterialCalcConfig.connectorUrl,
      params: {
        action: "mgr/setting/get",
        key: MaterialCalc.getProductMarkupKey(productId),
      },
      listeners: {
        success: {
          fn: function (response) {
            var value = 1;

            if (response && response.object && typeof response.object.value !== "undefined") {
              value = MaterialCalc.utils.makeNumber(response.object.value, 1);
            }

            if (value <= 0) {
              value = 1;
            }

            field.setValue(value);

            var store = this.getStore();
            var json = store && store.reader && store.reader.jsonData ? store.reader.jsonData : null;

            if (json && json.totals) {
              this.updateSummaryBlock(json.totals);
            } else {
              this.updateSummaryBlock(this.calculateTotalsFromStore());
            }
          },
          scope: this,
        },
        failure: {
          fn: function () {
            field.setValue(1);
            this.updateSummaryBlock(this.calculateTotalsFromStore());
          },
          scope: this,
        },
      },
    });
  },

  saveProductMarkup: function () {
    var productId = this.productId || MaterialCalc.getCurrentProductId();
    var field = this.getMarkupField();
    var value = field ? MaterialCalc.utils.makeNumber(field.getValue(), 1) : 1;

    if (!productId) {
      MODx.msg.alert("Ошибка", "Не удалось определить ID товара.");
      return;
    }

    if (value <= 0) {
      value = 1;
      if (field) {
        field.setValue(1);
      }
    }

    MODx.Ajax.request({
      url: MaterialCalcConfig.connectorUrl,
      params: {
        action: "mgr/setting/update",
        key: MaterialCalc.getProductMarkupKey(productId),
        value: value,
      },
      listeners: {
        success: {
          fn: function () {
            MODx.msg.status({
              title: "Готово",
              message: "Наценка сохранена",
            });
            this.refresh();
          },
          scope: this,
        },
        failure: {
          fn: function () {
            MODx.msg.alert("Ошибка", "Не удалось сохранить наценку");
          },
          scope: this,
        },
      },
    });
  },

  createMaterialLink: function (btn, e) {
    var productId =
      this.config.baseParams.product_id || MaterialCalc.getCurrentProductId();

    if (!productId) {
      MODx.msg.alert(
        "Ошибка",
        "Не удалось определить ID товара. Сначала сохраните товар."
      );
      return;
    }

    var w = MODx.load({
      xtype: "materialcalc-window-product-tab-material-create",
      product_id: productId,
      listeners: {
        success: {
          fn: function () {
            this.refresh();
          },
          scope: this,
        },
      },
    });

    w.show(e && e.target ? e.target : Ext.getBody());
  },

  getMenu: function () {
    return [
      {
        text: "Удалить материал из товара",
        handler: this.removeMaterialLink,
        scope: this,
      },
    ];
  },

  removeMaterialLink: function () {
    MODx.msg.confirm({
      title: "Удалить привязку",
      text: "Удалить этот материал из товара?",
      url: this.config.url,
      params: {
        action: "mgr/productmaterial/remove",
        id: this.menu.record.id,
      },
      listeners: {
        success: {
          fn: function () {
            this.refresh();
          },
          scope: this,
        },
      },
    });
  },
});

Ext.reg(
  "materialcalc-grid-product-tab-materials",
  MaterialCalc.grid.ProductTabMaterials
);

MaterialCalc.window.ProductTabMaterialCreate = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    title: "Добавить материал к товару",
    width: 520,
    url: MaterialCalcConfig.connectorUrl,
    action: "mgr/productmaterial/create",
    fields: [
      {
        xtype: "hidden",
        name: "product_id",
        value: config.product_id || MaterialCalc.getCurrentProductId(),
      },
      {
        xtype: "materialcalc-combo-material",
        fieldLabel: "Материал",
        name: "material_id",
        hiddenName: "material_id",
        anchor: "100%",
        allowBlank: false,
      },
      {
        xtype: "numberfield",
        fieldLabel: "Расход",
        name: "amount",
        anchor: "100%",
        decimalPrecision: 3,
        allowBlank: false,
        allowNegative: false,
        value: 1,
      },
      {
        xtype: "combo-boolean",
        fieldLabel: "Активен",
        name: "active",
        hiddenName: "active",
        value: 1,
      },
    ],
  });

  MaterialCalc.window.ProductTabMaterialCreate.superclass.constructor.call(
    this,
    config
  );
};

Ext.extend(MaterialCalc.window.ProductTabMaterialCreate, MODx.Window);
Ext.reg(
  "materialcalc-window-product-tab-material-create",
  MaterialCalc.window.ProductTabMaterialCreate
);

MaterialCalc.addProductMaterialsTab = function () {
  var tabs = Ext.getCmp("modx-resource-tabs");
  var productId = MaterialCalc.getCurrentProductId();

  if (!tabs || !productId) {
    return false;
  }

  if (Ext.getCmp("materialcalc-product-materials-tab")) {
    return true;
  }

  tabs.add({
    id: "materialcalc-product-materials-tab",
    title: "Материалы",
    layout: "anchor",
    autoHeight: true,
    autoScroll: true,
    bodyStyle: "padding:10px;",
    items: [
      {
        xtype: "materialcalc-grid-product-tab-materials",
        product_id: productId,
      },
    ],
  });

  tabs.doLayout();

  return true;
};

Ext.onReady(function () {
  var attempts = 0;

  var timer = setInterval(function () {
    attempts++;

    if (MaterialCalc.addProductMaterialsTab() || attempts > 30) {
      clearInterval(timer);
    }
  }, 300);
});
