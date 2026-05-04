Ext.namespace(
  "MaterialCalc",
  "MaterialCalc.grid",
  "MaterialCalc.window",
  "MaterialCalc.panel",
  "MaterialCalc.form",
  "MaterialCalc.combo",
  "MaterialCalc.utils"
);

/**
 * Helpers
 */
MaterialCalc.utils = {
  productLabel: function (id, pagetitle, article) {
    id = String(id || "");
    var label = "";

    if (id) {
      label = "#" + id;
    }

    if (pagetitle) {
      label += " — " + pagetitle;
    }

    if (article) {
      label += " / Арт.: " + article;
    }

    return label || id;
  },

  makeNumber: function (value, fallback) {
    if (value === null || typeof value === "undefined" || value === "") {
      return fallback || 0;
    }

    value = String(value).replace(",", ".");
    value = parseFloat(value);

    return isNaN(value) ? fallback || 0 : value;
  },

  formatNumber: function (value) {
    value = MaterialCalc.utils.makeNumber(value, 0);

    if (Math.round(value) === value) {
      return String(value);
    }

    return String(Math.round(value * 100) / 100);
  },

  setComboValueSafe: function (combo, value, rawValue) {
    value = String(value || "");
    rawValue = String(rawValue || "");

    combo.setValue(value);

    if (rawValue) {
      combo.setRawValue(rawValue);
    }

    if (combo.hiddenField) {
      combo.hiddenField.value = value;
    }

    if (combo.el && combo.el.dom && rawValue) {
      combo.el.dom.value = rawValue;
    }

    combo.materialcalcLastValue = value;
    combo.materialcalcLastRaw = rawValue;

    if (combo.clearInvalid) {
      combo.clearInvalid();
    }
  },

  materialWindowFields: function (record) {
    record = record || {};

    return [
      {
        xtype: "hidden",
        name: "id",
        value: record.id || 0,
        hidden: !record.id,
      },
      {
        xtype: "textfield",
        fieldLabel: "Название материала",
        name: "name",
        anchor: "100%",
        allowBlank: false,
        value: record.name || "",
      },
      {
        xtype: "numberfield",
        fieldLabel: "Цена м2/шт",
        name: "price",
        anchor: "100%",
        decimalPrecision: 2,
        allowNegative: false,
        allowBlank: false,
        value: MaterialCalc.utils.makeNumber(record.price, 0),
      },
      {
        xtype: "combo-boolean",
        fieldLabel: "Активен",
        name: "active",
        hiddenName: "active",
        width: 130,
        value: Number(record.active) === 0 ? 0 : 1,
      },
      {
        xtype: "numberfield",
        fieldLabel: "Сортировка",
        name: "rank",
        anchor: "100%",
        allowNegative: false,
        value: MaterialCalc.utils.makeNumber(record.rank, 0),
      },
    ];
  },
};

/**
 * Combo: Product
 */
MaterialCalc.combo.Product = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    name: config.name || "product_id_display",
    hiddenName: config.hiddenName || "product_id",
    displayField: "label",
    valueField: "id",
    fields: ["id", "pagetitle", "article", "price", "label"],
    url: MaterialCalcConfig.connectorUrl,
    baseParams: {
      action: "mgr/product/getlist",
      combo: 1,
    },
    queryParam: "query",
    pageSize: 20,
    mode: "remote",
    typeAhead: false,
    editable: true,
    forceSelection: true,
    triggerAction: "all",
    allQuery: "",
    minChars: 0,
    queryDelay: 300,
    emptyText: "Начните вводить название, артикул или ID товара",
    valueNotFoundText: "",
    tpl: new Ext.XTemplate(
      '<tpl for=".">',
      '<div class="x-combo-list-item">',
      "<b>#{id}</b>",
      '<tpl if="pagetitle"> — {pagetitle}</tpl>',
      '<tpl if="!pagetitle && label"> — {label}</tpl>',
      '<tpl if="article"><br><span style="color:#777;">Артикул: {article}</span></tpl>',
      '<tpl if="price"><br><span style="color:#777;">Цена: {price}</span></tpl>',
      "</div>",
      "</tpl>"
    ),
    listeners: {
      beforequery: function (qe) {
        if (qe && qe.forceAll) {
          qe.query = "";
        }
      },
      afterrender: function (combo) {
        if (!combo.materialcalcLastValue && combo.getValue()) {
          combo.materialcalcLastValue = String(combo.getValue());
        }

        if (!combo.materialcalcLastRaw && combo.getRawValue()) {
          combo.materialcalcLastRaw = combo.getRawValue();
        }

        if (combo.hiddenField && combo.materialcalcLastValue) {
          combo.hiddenField.value = combo.materialcalcLastValue;
        }
      },
      focus: function (combo) {
        if (combo.getValue()) {
          combo.materialcalcLastValue = String(combo.getValue());
        }

        if (combo.getRawValue()) {
          combo.materialcalcLastRaw = combo.getRawValue();
        }

        if (combo.hiddenField && combo.materialcalcLastValue) {
          combo.hiddenField.value = combo.materialcalcLastValue;
        }
      },
      select: function (combo, record) {
        var id = String(record.get("id"));
        var label = record.get("label");

        if (!label) {
          label = MaterialCalc.utils.productLabel(
            id,
            record.get("pagetitle"),
            record.get("article")
          );
        }

        MaterialCalc.utils.setComboValueSafe(combo, id, label);
      },
      blur: function (combo) {
        var value = combo.getValue();
        var raw = combo.getRawValue();

        if ((!value || value === raw) && combo.materialcalcLastValue) {
          MaterialCalc.utils.setComboValueSafe(
            combo,
            combo.materialcalcLastValue,
            combo.materialcalcLastRaw
          );
          return;
        }

        if (value) {
          combo.materialcalcLastValue = String(value);
        }

        if (raw) {
          combo.materialcalcLastRaw = raw;
        }

        if (combo.hiddenField && combo.materialcalcLastValue) {
          combo.hiddenField.value = combo.materialcalcLastValue;
        }

        combo.clearInvalid();
      },
    },
  });

  MaterialCalc.combo.Product.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.combo.Product, MODx.combo.ComboBox);
Ext.reg("materialcalc-combo-product", MaterialCalc.combo.Product);

/**
 * Combo: Material
 */
MaterialCalc.combo.Material = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    name: config.name || "material_id_display",
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
    emptyText: "Выберите материал из справочника",
    tpl: new Ext.XTemplate(
      '<tpl for=".">',
      '<div class="x-combo-list-item">',
      "<b>{name}</b>",
      '<tpl if="price"><br><span style="color:#777;">Цена м2/шт: {price}</span></tpl>',
      "</div>",
      "</tpl>"
    ),
    listeners: {
      select: function (combo, record) {
        var id = String(record.get("id"));
        var label = record.get("name");

        MaterialCalc.utils.setComboValueSafe(combo, id, label);
      },
    },
  });

  MaterialCalc.combo.Material.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.combo.Material, MODx.combo.ComboBox);
Ext.reg("materialcalc-combo-material", MaterialCalc.combo.Material);

/**
 * Grid: Materials
 */
MaterialCalc.grid.Materials = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    id: "materialcalc-grid-materials",
    url: MaterialCalcConfig.connectorUrl,
    baseParams: {
      action: "mgr/material/getlist",
    },
    fields: ["id", "name", "price", "active", "rank"],
    paging: true,
    remoteSort: true,
    preventRender: true,
    autoHeight: false,
    height: 460,
    autoExpandColumn: "materialcalc-col-material-name",
    autosave: false,
    viewConfig: {
      forceFit: true,
      emptyText: "Материалы ещё не добавлены",
    },
    columns: [
      {
        header: "ID",
        dataIndex: "id",
        width: 45,
        sortable: true,
      },
      {
        header: "Название материала",
        dataIndex: "name",
        id: "materialcalc-col-material-name",
        width: 260,
        sortable: true,
      },
      {
        header: "Цена м2/шт",
        dataIndex: "price",
        width: 110,
        sortable: true,
      },
      {
        header: "Активен",
        dataIndex: "active",
        width: 70,
        sortable: true,
        renderer: function (value) {
          return Number(value) === 1 ? "Да" : "Нет";
        },
      },
      {
        header: "Сортировка",
        dataIndex: "rank",
        width: 85,
        sortable: true,
      },
    ],
    tbar: [
      {
        text: "Добавить материал",
        cls: "primary-button",
        handler: this.createMaterial,
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
      rowdblclick: {
        fn: function (grid, rowIndex, e) {
          var record = grid.getStore().getAt(rowIndex);

          grid.menu = grid.menu || {};
          grid.menu.record = record.data || record;

          grid.updateMaterial(null, e);
        },
        scope: this,
      },
    },
  });

  MaterialCalc.grid.Materials.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.grid.Materials, MODx.grid.Grid, {
  createMaterial: function (btn, e) {
    var w = MODx.load({
      xtype: "materialcalc-window-material-create",
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

  updateMaterial: function (btn, e) {
    var record = this.menu.record;

    var w = MODx.load({
      xtype: "materialcalc-window-material-update",
      record: record,
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
        text: "Редактировать материал",
        handler: this.updateMaterial,
        scope: this,
      },
      "-",
      {
        text: "Удалить материал",
        handler: this.removeMaterial,
        scope: this,
      },
    ];
  },

  removeMaterial: function () {
    MODx.msg.confirm({
      title: "Удалить материал",
      text: "Вы действительно хотите удалить этот материал?",
      url: this.config.url,
      params: {
        action: "mgr/material/remove",
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

Ext.reg("materialcalc-grid-materials", MaterialCalc.grid.Materials);

/**
 * Window: Material Create
 */
MaterialCalc.window.MaterialCreate = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    title: "Добавить материал",
    width: 620,
    labelWidth: 150,
    url: MaterialCalcConfig.connectorUrl,
    action: "mgr/material/create",
    fields: MaterialCalc.utils.materialWindowFields({
      active: 1,
      price: 0,
      rank: 0,
    }),
  });

  MaterialCalc.window.MaterialCreate.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.window.MaterialCreate, MODx.Window);
Ext.reg("materialcalc-window-material-create", MaterialCalc.window.MaterialCreate);

/**
 * Window: Material Update
 */
MaterialCalc.window.MaterialUpdate = function (config) {
  config = config || {};

  var rawRecord = config.record || {};
  var record = rawRecord.data || rawRecord;

  Ext.applyIf(config, {
    title: "Редактировать материал",
    width: 620,
    labelWidth: 150,
    url: MaterialCalcConfig.connectorUrl,
    action: "mgr/material/update",
    fields: MaterialCalc.utils.materialWindowFields(record),
  });

  MaterialCalc.window.MaterialUpdate.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.window.MaterialUpdate, MODx.Window);
Ext.reg("materialcalc-window-material-update", MaterialCalc.window.MaterialUpdate);

/**
 * Form: Settings
 */
MaterialCalc.form.Settings = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    id: "materialcalc-form-settings",
    border: false,
    autoHeight: true,
    bodyStyle: "padding:0;",
    items: [
      {
        xtype: "panel",
        border: false,
        autoHeight: true,
        bodyStyle: "padding:20px; max-width:760px;",
        items: [
          {
            xtype: "fieldset",
            title: "Настройки вывода",
            autoHeight: true,
            width: 720,
            labelWidth: 220,
            defaults: {
              msgTarget: "under",
            },
            items: [
              {
                xtype: "combo-boolean",
                fieldLabel: "Выводить материалы на фронте",
                name: "show_frontend",
                hiddenName: "show_frontend",
                id: "materialcalc-setting-show-frontend",
                width: 120,
                value: 0,
              },
              {
                xtype: "displayfield",
                fieldLabel: "Что делает настройка",
                value:
                  '<div style="color:#777;line-height:1.5;max-width:460px;">Если включено — на странице товара будет виден блок материалов и расчёта. Если выключено — компонент только рассчитает цену и передаст её в карточку товара, но HTML-блок материалов на фронте выводиться не будет.</div>',
              },
              {
                xtype: "displayfield",
                fieldLabel: "Формула",
                value:
                  '<div style="color:#555;line-height:1.5;max-width:460px;">Себестоимость = цена материала × расход.</div>',
              },
            ],
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Сохранить настройки",
        handler: function () {
          this.saveSettings();
        },
        scope: this,
      },
    ],
    listeners: {
      afterrender: {
        fn: function () {
          this.loadSettings();
        },
        scope: this,
      },
    },
  });

  MaterialCalc.form.Settings.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.form.Settings, MODx.Panel, {
  loadSettings: function () {
    var showFrontendField = Ext.getCmp("materialcalc-setting-show-frontend");

    MODx.Ajax.request({
      url: MaterialCalcConfig.connectorUrl,
      params: {
        action: "mgr/setting/get",
        key: "show_frontend",
      },
      listeners: {
        success: {
          fn: function (response) {
            if (showFrontendField && response && response.object) {
              showFrontendField.setValue(Number(response.object.value) === 1 ? 1 : 0);
            }
          },
          scope: this,
        },
        failure: {
          fn: function () {
            if (showFrontendField) {
              showFrontendField.setValue(0);
            }
          },
          scope: this,
        },
      },
    });
  },

  saveSettings: function () {
    var showFrontendField = Ext.getCmp("materialcalc-setting-show-frontend");
    var showFrontend = showFrontendField ? showFrontendField.getValue() : 0;

    MODx.Ajax.request({
      url: MaterialCalcConfig.connectorUrl,
      params: {
        action: "mgr/setting/update",
        key: "show_frontend",
        value: Number(showFrontend) === 1 ? 1 : 0,
      },
      listeners: {
        success: {
          fn: function () {
            MODx.msg.status({
              title: "Готово",
              message: "Настройки сохранены",
            });
          },
          scope: this,
        },
        failure: {
          fn: function () {
            MODx.msg.alert("Ошибка", "Не удалось сохранить настройки");
          },
          scope: this,
        },
      },
    });
  },
});

Ext.reg("materialcalc-form-settings", MaterialCalc.form.Settings);

/**
 * Grid: Product Materials
 */
MaterialCalc.grid.ProductMaterials = function (config) {
  config = config || {};

  var tbarItems = [
    {
      text: "Добавить материал",
      cls: "primary-button",
      handler: this.createProductMaterial,
      scope: this,
    },
    {
      xtype: "textfield",
      id: "materialcalc-productmaterials-search",
      emptyText: "Поиск: товар, материал, артикул или ID",
      width: 300,
      listeners: {
        specialkey: {
          fn: function (field, e) {
            if (e.getKey() === e.ENTER) {
              this.getStore().baseParams.query = field.getValue();
              this.getBottomToolbar().changePage(1);
            }
          },
          scope: this,
        },
      },
    },
    {
      text: "Найти",
      handler: function () {
        var field = Ext.getCmp("materialcalc-productmaterials-search");
        this.getStore().baseParams.query = field ? field.getValue() : "";
        this.getBottomToolbar().changePage(1);
      },
      scope: this,
    },
    {
      text: "Сбросить",
      handler: function () {
        var field = Ext.getCmp("materialcalc-productmaterials-search");

        if (field) {
          field.setValue("");
        }

        this.getStore().baseParams.query = "";
        this.getBottomToolbar().changePage(1);
      },
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
  ];

  Ext.applyIf(config, {
    id: "materialcalc-grid-product-materials",
    url: MaterialCalcConfig.connectorUrl,
    baseParams: {
      action: "mgr/productmaterial/getlist",
    },
    fields: [
      "id",
      "product_id",
      "product_name",
      "product_article",
      "product_price",
      "material_id",
      "material_name",
      "material_price",
      "amount",
      "row_sum",
      "active",
    ],
    paging: true,
    remoteSort: true,
    preventRender: true,
    autoHeight: false,
    height: 460,
    save_action: "mgr/productmaterial/update",
    autosave: true,
    viewConfig: {
      forceFit: true,
      emptyText: "Материалы к товарам ещё не добавлены",
    },
    columns: [
      {
        header: "ID",
        dataIndex: "id",
        width: 50,
        sortable: true,
      },
      {
        header: "Товар",
        dataIndex: "product_name",
        width: 320,
        sortable: true,
        renderer: function (value, cell, row) {
          var id = row.data.product_id || "";

          if (!value) {
            return "#" + id;
          }

          return "#" + id + " — " + value;
        },
      },
      {
        header: "Артикул",
        dataIndex: "product_article",
        width: 120,
        sortable: true,
        renderer: function (value) {
          return value || "";
        },
      },
      {
        header: "Цена товара",
        dataIndex: "product_price",
        width: 100,
        sortable: true,
        renderer: function (value) {
          return value || "";
        },
      },
      {
        header: "Материал",
        dataIndex: "material_name",
        width: 220,
        sortable: true,
      },
      {
        header: "Цена м2/шт",
        dataIndex: "material_price",
        width: 110,
        sortable: true,
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
        width: 120,
        sortable: false,
        renderer: function (value, cell, row) {
          var price = MaterialCalc.utils.makeNumber(row.data.material_price, 0);
          var amount = MaterialCalc.utils.makeNumber(row.data.amount, 0);
          var sum = price * amount;

          if (!sum) {
            sum = MaterialCalc.utils.makeNumber(value, 0);
          }

          return MaterialCalc.utils.formatNumber(sum);
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
    tbar: tbarItems,
    listeners: {
      rowdblclick: {
        fn: function (grid, rowIndex, e) {
          var record = grid.getStore().getAt(rowIndex);

          grid.menu = grid.menu || {};
          grid.menu.record = record.data || record;

          grid.updateProductMaterial(null, e);
        },
        scope: this,
      },
    },
  });

  MaterialCalc.grid.ProductMaterials.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.grid.ProductMaterials, MODx.grid.Grid, {
  createProductMaterial: function (btn, e) {
    var w = MODx.load({
      xtype: "materialcalc-window-product-material-create",
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

  updateProductMaterial: function (btn, e) {
    var record = this.menu.record;

    var w = MODx.load({
      xtype: "materialcalc-window-product-material-update",
      record: record,
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
        text: "Редактировать материал товара",
        handler: this.updateProductMaterial,
        scope: this,
      },
      "-",
      {
        text: "Удалить материал товара",
        handler: this.removeProductMaterial,
        scope: this,
      },
    ];
  },

  removeProductMaterial: function () {
    MODx.msg.confirm({
      title: "Удалить материал товара",
      text: "Вы действительно хотите удалить этот материал из товара?",
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

Ext.reg("materialcalc-grid-product-materials", MaterialCalc.grid.ProductMaterials);

/**
 * Window: Product Material Create
 */
MaterialCalc.window.ProductMaterialCreate = function (config) {
  config = config || {};

  var productId = parseInt(config.product_id || config.productId || 0, 10) || 0;
  var fields = [];

  if (productId > 0) {
    fields.push({
      xtype: "hidden",
      name: "product_id",
      value: productId,
    });
  } else {
    fields.push({
      xtype: "materialcalc-combo-product",
      fieldLabel: "Товар",
      name: "product_id_display",
      hiddenName: "product_id",
      anchor: "100%",
      allowBlank: false,
    });
  }

  fields = fields.concat([
    {
      xtype: "materialcalc-combo-material",
      fieldLabel: "Материал",
      name: "material_id_display",
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
      width: 130,
      value: 1,
    },
  ]);

  Ext.applyIf(config, {
    title: "Добавить материал к товару",
    width: 700,
    labelWidth: 120,
    url: MaterialCalcConfig.connectorUrl,
    action: "mgr/productmaterial/create",
    fields: fields,
  });

  MaterialCalc.window.ProductMaterialCreate.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.window.ProductMaterialCreate, MODx.Window);
Ext.reg("materialcalc-window-product-material-create", MaterialCalc.window.ProductMaterialCreate);

/**
 * Window: Product Material Update
 */
MaterialCalc.window.ProductMaterialUpdate = function (config) {
  config = config || {};

  var rawRecord = config.record || {};
  var record = rawRecord.data || rawRecord;
  var gridProductId = parseInt(config.product_id || config.productId || 0, 10) || 0;

  var productId = String(gridProductId || record.product_id || "");
  var productName = record.product_name || "";
  var productArticle = record.product_article || "";
  var productPrice = record.product_price || "";

  var materialId = String(record.material_id || "");
  var materialName = record.material_name || "";
  var materialPrice = record.material_price || "";

  var productLabel = MaterialCalc.utils.productLabel(productId, productName, productArticle);
  var fields = [
    {
      xtype: "hidden",
      name: "id",
      value: record.id || 0,
    },
  ];

  if (gridProductId > 0) {
    fields.push({
      xtype: "hidden",
      name: "product_id",
      value: gridProductId,
    });
  } else {
    fields.push({
      xtype: "materialcalc-combo-product",
      fieldLabel: "Товар",
      name: "product_id_display",
      hiddenName: "product_id",
      anchor: "100%",
      allowBlank: false,
      value: productId,
      listeners: {
        afterrender: function (combo) {
          var store = combo.getStore ? combo.getStore() : combo.store;

          if (store && productId) {
            var RecordType = store.recordType;
            var existingIndex = store.findExact ? store.findExact("id", productId) : -1;

            if (existingIndex < 0 && RecordType) {
              var productRecord = new RecordType(
                {
                  id: productId,
                  pagetitle: productName,
                  article: productArticle,
                  price: productPrice,
                  label: productLabel,
                },
                productId
              );

              store.add([productRecord]);
            }
          }

          MaterialCalc.utils.setComboValueSafe(combo, productId, productLabel);

          (function () {
            MaterialCalc.utils.setComboValueSafe(combo, productId, productLabel);
          }).defer(250);
        },
        select: function (combo, rec) {
          var id = String(rec.get("id"));
          var label = rec.get("label");

          if (!label) {
            label = MaterialCalc.utils.productLabel(id, rec.get("pagetitle"), rec.get("article"));
          }

          MaterialCalc.utils.setComboValueSafe(combo, id, label);

          productId = id;
          productLabel = label;
        },
      },
    });
  }

  fields = fields.concat([
    {
      xtype: "materialcalc-combo-material",
      fieldLabel: "Материал",
      name: "material_id_display",
      hiddenName: "material_id",
      anchor: "100%",
      allowBlank: false,
      value: materialId,
      listeners: {
        afterrender: function (combo) {
          var store = combo.getStore ? combo.getStore() : combo.store;

          if (store && materialId) {
            var RecordType = store.recordType;
            var existingIndex = store.findExact ? store.findExact("id", materialId) : -1;

            if (existingIndex < 0 && RecordType) {
              var materialRecord = new RecordType(
                {
                  id: materialId,
                  name: materialName,
                  price: materialPrice,
                },
                materialId
              );

              store.add([materialRecord]);
            }
          }

          MaterialCalc.utils.setComboValueSafe(combo, materialId, materialName);

          (function () {
            MaterialCalc.utils.setComboValueSafe(combo, materialId, materialName);
          }).defer(250);
        },
        select: function (combo, rec) {
          var id = String(rec.get("id"));
          var label = rec.get("name");

          MaterialCalc.utils.setComboValueSafe(combo, id, label);
        },
      },
    },
    {
      xtype: "numberfield",
      fieldLabel: "Расход",
      name: "amount",
      anchor: "100%",
      decimalPrecision: 3,
      allowBlank: false,
      allowNegative: false,
      value: MaterialCalc.utils.makeNumber(record.amount, 1),
    },
    {
      xtype: "combo-boolean",
      fieldLabel: "Активен",
      name: "active",
      hiddenName: "active",
      width: 130,
      value: Number(record.active) === 1 ? 1 : 0,
    },
  ]);

  Ext.applyIf(config, {
    title: "Редактировать материал товара",
    width: 700,
    labelWidth: 120,
    url: MaterialCalcConfig.connectorUrl,
    action: "mgr/productmaterial/update",
    fields: fields,
  });

  MaterialCalc.window.ProductMaterialUpdate.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.window.ProductMaterialUpdate, MODx.Window);
Ext.reg("materialcalc-window-product-material-update", MaterialCalc.window.ProductMaterialUpdate);

/**
 * Panel: Home
 */
MaterialCalc.panel.Home = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    id: "materialcalc-panel-main",
    border: false,
    cls: "container",
    items: [
      {
        xtype: "modx-tabs",
        id: "materialcalc-tabs",
        border: true,
        defaults: {
          border: false,
          autoHeight: true,
        },
        items: [
          {
            title: "Материалы",
            layout: "fit",
            bodyStyle: "padding:10px;",
            items: [
              {
                xtype: "materialcalc-grid-materials",
              },
            ],
          },
          {
            title: "Привязки к товарам",
            layout: "fit",
            bodyStyle: "padding:10px;",
            items: [
              {
                xtype: "materialcalc-grid-product-materials",
              },
            ],
          },
          {
            title: "Настройки",
            layout: "fit",
            bodyStyle: "padding:10px;",
            items: [
              {
                xtype: "materialcalc-form-settings",
              },
            ],
          },
        ],
      },
    ],
  });

  MaterialCalc.panel.Home.superclass.constructor.call(this, config);
};

Ext.extend(MaterialCalc.panel.Home, MODx.Panel);
Ext.reg("materialcalc-panel-main", MaterialCalc.panel.Home);

/**
 * Init
 */
Ext.onReady(function () {
  if (typeof MaterialCalcConfig === "undefined" || !MaterialCalcConfig.connectorUrl) {
    Ext.get("materialcalc-panel-home").update(
      '<div style="padding:20px;color:red;">MaterialCalcConfig.connectorUrl не найден</div>'
    );
    return;
  }

  MODx.load({
    xtype: "materialcalc-panel-main",
    renderTo: "materialcalc-panel-home",
  });
});
