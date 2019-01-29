# 可複製表單

## 說明

大部份設定同一般表單元件

僅config.coffee有不同

**notNull**: 在前端驗證的必填欄位

```coffeescript
module.exports = [
  {
    name: "setting"
    text: "測試可複製表單"
    type: "repeatedform"
    config: [
      {
        name: "name"
        text: "名稱"
        type: "input"
        notNull: true
      }
      {
        name: "amount"
        text: "數量"
        type: "number"
        notNull: true
      }
      {
        name: "types"
        text: "付款方式"
        type: "choice"
        notNull: true
        config:
          api: "test.choice"
      }
      {
        name: "category"
        text: "產品分類"
        type: "selector"
        notNull: true
        config:
          api: "test.productCategories"
      }
    ]
  }
]
```

## 編輯模式

可參考一般表單的編輯模式

大部份同一般表單

### 編輯模式 - type:

html: html編輯器

input: 文字欄位, config要加上placeholder, 如果要使用唯讀, 加上readonly: true屬性

selector: 一般式下拉選單, 需使用回傳一個promise並resolve資料, [資料格式參考](https://demo1255810.mockable.io/product-edit-tags)

groupselector: 群組式下拉選單, 需使用回傳一個promise並resolve資料, [資料格式參考](https://demo1255810.mockable.io/product-edit-categories)

switch: 狀態切換, config中設定on(啟用)以及off(停用)的文字

choices: 下拉選單, 必須為一個promise

date: 日期