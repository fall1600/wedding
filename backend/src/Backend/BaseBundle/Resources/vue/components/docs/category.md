# 分類元件

## 使用方式

 在bundle下的components/router/category資料夾中建立一個vue
```html
<template>
    <div class="page-content">
        <category></category>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mixins: [require "components/backendbase/mixins/categoryConfig.coffee"]
    mounted: () ->
        @$store.dispatch 'pageTitle.update', '測試分類'
    methods:
        getModel: () ->
            require('components/backendbase/actions/category/test.coffee')
</script>

```

## 載入Action

在bundle下的components/actions/category資料夾中建立一個coffee

並在_init()中載入api
 
```coffeescript

abstractCategory = require "components/backendbase/actions/abstract-category.coffee"

class TestCategory extends abstractCategory
  _init: () ->
    require('components/backendbase/actions/api/test.coffee') @api
  # 如果有要限制分類的層數再使用, 沒有的話可以不用使用config
  getConfig: () ->
    require 'components/widgetproduct/config/category/product.coffee'
  getRoot: () ->
    @api.test.getRoot()
  getChildren: (id) ->
    @api.test.getChildren id
  # 新增分類, data.target為要新增分類的父分類id
  create: (data) ->
    @api.test.createCategory data
  # 更新分類, data.id為要更新的分類id
  update: (data) ->
    @api.test.updateCategory data
  delete: (id) ->
    @api.test.deleteCategory id
  # move為被移動的id, target為目標的id
  moveInside: (move, target) ->
    @api.test.moveInside(move, target)
  # move為被移動的id, target為目標的id
  moveAfter: (move, target) ->
    @api.test.moveAfter(move, target)

module.exports = (api) ->
  new TestCategory(api)
```

## config範例

這邊以商品分類為範例

參考路徑: components/widgetproduct/config/category/product.coffee

範例

```coffeescript
module.exports =
  levelLimit: 2
```

## API回傳範例

### getRoot

取得根節點

```json
{
	"id": "11",
	"name": "根目錄",
	"status": true,
	"data": {}
}
```

### getChildren

取得子分類

```json
[
  {
    "id": 111,
    "name": "衣著",
    "status": true,
    "data": {}
  },
  {
    "id": 222,
    "name": "食物",
    "status": true,
    "data": {}
  },
  {
    "id": 333,
    "name": "書籍",
    "status": true,
    "data": {}
  }
]
```



