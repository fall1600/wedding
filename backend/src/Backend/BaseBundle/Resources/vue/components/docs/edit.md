# 編輯元件

## 使用方式

假設目前要新增一個商品編輯頁面

**在cm4/vue/cm4/components/backendbase/router/edit中建立一個 product.vue**

```html
<template>
    <div class="page-content">
        <!-- useCustomize: 載入客製元件 -->
        <edit :editSetting="editSetting" :useCustomize="customize"></edit>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mixins: [require "components/backendbase/mixins/editConfig.coffee"]
    mounted: () ->
#       設定頁面標題 
        @$store.dispatch 'pageTitle.update', '商品編輯'
    data: () ->
#       載入客製元件, 如果沒有要使用就留空物件
        customize: []
    methods:
#       設定coffee來源
        getModel: () ->
            require('components/backendbase/actions/edit/product.coffee')
</script>

```
## 客製coffee

**在cm4/vue/cm4/components/backendbase/actions/edit中加入product.coffee**

* 在init()中, 載入api
* 在getConfig()中, 載入config設定
* 在_getData()中取得資料, 如果是要建立"新增"的表單, 就不需要設定, route導向**/product-new**不帶ID即可
* 表單所有更新的資料, 都在@editSetting.dataRow中

```coffeescript
abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class productEdit extends abstractEdit
  _init: () ->
    require("components/backendbase/actions/edit/customapi.coffee") @api
#    require("components/backendbase/actions/edit/fakeapi.coffee") @api
  getConfig: () ->
    return require 'components/backendbase/config/edit/product.coffee'
# 取得資料, 帶入id
  _getData: (id) ->
    return @api.read id
# 儲存表單
  save: () ->
    return @api.productUpdate @editSetting.dataRow
module.exports = (api, dataId) ->
  new productEdit(api, dataId)
```

**api範例**

```coffeescript
module.exports = (api) ->
  api.article =
    search: () ->
      api.request "GET", "/product"
    create: (data) ->
      api.request "POST", "/product", data
    read: (id) ->
      api.request "GET", "/product/#{id}"
    update: (id, data) ->
      api.request  "PUT", "/product/#{id}", data
    delete: (id) ->
      api.request  "DELETE", "/product/#{id}"

```

## 設定config

**在cm4/vue/cm4/components/backendbase/config/edit中加入product.coffee**

如果是子表單來源需要callback(例如下拉選單的選項), 就回傳一個promise

### 編輯模式 - type:

html: html編輯器

input: 文字欄位, config要加上placeholder, 如果要使用唯讀, 加上readonly: true屬性

photo: 圖片上傳, config要加上最多上傳張數(max), 最少上傳張數(min), 縮圖模式(configType)

selector: 一般式下拉選單, 需使用回傳一個promise並resolve資料, [資料格式參考](https://demo1255810.mockable.io/product-edit-tags)

groupselector: 群組式下拉選單, 需使用回傳一個promise並resolve資料, [資料格式參考](https://demo1255810.mockable.io/product-edit-categories)

switch: 狀態切換, config中設定on(啟用)以及off(停用)的文字

choices: 下拉選單, 必須為一個promise, config中可設定權限(roles)以及選單資料(data), 如果roles沒設定, 則完全開放

date: 日期

```coffeescript
module.exports =
[
  {
    "name": "productName",
    "text": "商品名稱",
    "type": "input",
    "readonly": true,
    "config": {
      "placeholder": "請輸入商品名稱"
    }
  },
  {
    "name": "info",
    "text": "商品詳細介紹",
    "type": "html"
  },
  {
    "name": "categories",
    "text": "商品分類",
    "type": "groupselector",
    "config": 
      api: "test.categories"
  },
  {
    "name": "photos",
    "text": "商品圖片",
    "type": "photos",
    "config": {
      "max": 5,
      "min": 0,
      "configType": ""
    }
  },
  {
    "name": "tags",
    "text": "商品標籤",
    "type": "selector",
    "config": config:
      api: "test.tag"
  },
  {
    "name": "summary",
    "text": "商品簡短說明",
    "type": "textarea"
  },
  {
    "name": "title",
    "text": "index.article.title",
    "type": "choice"
    "config": 
      api: "test.choice"
  },
  {
    "name": "status",
    "text": "商品狀態",
    "type": "switch",
    "config": {
      "on": "上架中",
      "off": "已下架"
    }
  }
]
```


## 資料來源

```json
{
  "id": "46781266",
  "productName": "Product B",
  "categories": [
    {
      "id": "456819",
      "name": "牛仔褲"
    },
    {
      "id": "123684",
      "name": "短褲"
    }
  ],
  "tags": [
    {
      "id": "98617473",
      "name": "英倫風格"
    },
    {
      "id": "85157429",
      "name": "韓風"
    }
  ],
  "photos": [
    { "url": "https://goo.gl/3tEKX2 "},
    { "url": "https://goo.gl/dYgKfv "},
    { "url": "https://goo.gl/E5kO0e "}
  ],
  "summary": "product summary",
  "status": true,
  "info": "Product B - 150NTD<span>390NTD</span><br>asdfas"
}
```

## 設定router.json

**路徑: cm4/src/BaseBundle/Resources/vue/router.json**

```json
{
    "path": "/product-edit/:id",
    "name": "product-edit",
    "component": "components/backendbase/router/edit/product.vue"
}
```

## bulid router

**sf dgfactor:assets:install**

## 照片上傳API

response同config中的photos

```coffeescript
{ "url": "https://goo.gl/dYgKfv "}
```
