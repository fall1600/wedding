# 子表單

![demo圖片] (https://goo.gl/6sQKxC)

## 說明

大部份設定同一般表單元件

僅config.coffee有不同

```coffeescript
module.exports = [
  {
    name: "payment"
    text: "form.bundle.WidgetPaymentBundle"
    type: "subform"
    config: [
      {
        // 分頁名稱
        name: "AllpayCredit"
        
        // 分頁文字(可使用語系)
        text: "form.label.setup.payment.allpay.credit"
        
        // 分頁內的子表單內容, 同一般表單設定
        content: [
          {
            name: "store_id"
            text: "form.label.setup.payment.store_id"
            type: "input"
          }

          {
            name: "hash_key"
            text: "form.label.setup.payment.allpay.hash_key"
            type: "input"
          }

          {
            name: "hash_iv"
            text: "form.label.setup.payment.allpay.hash_iv"
            type: "input"
          }
        ]
      }

      {
        // 分頁名稱
        name: "AllpayCvs"
        
        // 分頁文字(可使用語系)
        text: "form.label.setup.payment.allpay.vacc"
        
        // 分頁內的子表單內容, 同一般表單設定
        content: [
          {
            name: "store_id"
            text: "form.label.setup.payment.store_id"
            type: "input"
          }

          {
            name: "hash_key"
            text: "form.label.setup.payment.allpay.hash_key"
            type: "input"
          }

          {
            name: "hash_iv"
            text: "form.label.setup.payment.allpay.hash_iv"
            type: "input"
          }
        ]
      }
    ]
  }
]
```

## 編輯模式

可參考一般表單的編輯模式

大部份同一般表單

除了圖片(type: photo)外

其他都可以使用

### 編輯模式 - type:

html: html編輯器

input: 文字欄位, config要加上placeholder, 如果要使用唯讀, 加上readonly: true屬性

selector: 一般式下拉選單, 需使用回傳一個promise並resolve資料, [資料格式參考](https://demo1255810.mockable.io/product-edit-tags)

groupselector: 群組式下拉選單, 需使用回傳一個promise並resolve資料, [資料格式參考](https://demo1255810.mockable.io/product-edit-categories)

switch: 狀態切換, config中設定on(啟用)以及off(停用)的文字

choices: 下拉選單, 必須為一個promise

date: 日期