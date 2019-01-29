# 建立列表元件

## 載入元件

假設要建立的是帳號列表

在cm4/vue/cm4/components/backendbase/router/list中建立一個 siteuser.vue

```html
<template>
    <div class="page-content">
        <!--列表元件-->
        <list></list>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        mounted: () ->
            @$store.dispatch 'pageTitle.update', '帳號列表'
        mixins:  [require "components/backendbase/mixins/listConfig.coffee"]
        methods:
            listConfig: () ->
                require "components/backendbase/actions/list/siteuser.coffee"
            routerChange: () ->
                @init()
            init: () ->
                return
</script>
```

## 新增客製coffee

在cm4/vue/cm4/components/backendbase/actions/list中加入siteuser.coffee

可直接客製下列事件

```coffeescript
abstractList = require "components/backendbase/actions/abstract-list.coffee"

class SiteUserList extends abstractList
  _init: () ->
    require('components/backendbase/actions/api/siteuser.coffee') @api
  _getConfig: () ->
    require('components/backendbase/config/list/siteuser.coffee')
# 資料請求
  request: () -> @api.siteuser.search()

# 快速編輯, 切換狀態
  quickEdit: (id, data) -> @api.siteuser.update id, data

# 刪除資料
  deleteData: (id) -> @api.siteuser.delete id

module.exports = (api) ->
  new SiteUserList(api)
```

## 設定config json

設定客製coffee中configData的來源

在cm4/vue/cm4/components/backendbase/config/list中加入siteuser.coffeescript

* label: 資料顯示的名稱
* type: 資料顯示的型態, 除了image, checkbox外, 可使用html input的(text, number, date, datetime-local, time)
* sort: 允許排序的資料
* quick: 允許快速編輯的資料
* search: 允許被當為搜尋條件的資料
* batch: 允許被批次管理切換的資料
* extra:  可載入上方的客製元件, 例如路徑連結或是按鈕
* action: 動作按鈕, 動態由vue元件載入, 如果使用route屬性則為連結
* roles: 該元件允許的權限

設定皆需對應資料的key

```coffeescript
module.exports =
{
  "list": {
    "id": {
      "label": "#",
      "type": "text",
      "sort": true,
      "search": true
    },
    "first_name": {
      "label": "index.site_user.first_name",
      "type": "text",
      "sort": true,
      "quick": true,
      "search": true
    },
    "last_name": {
      "label": "index.site_user.last_name",
      "type": "text",
      "sort": true,
      "quick": true,
      "search": true
    },
    "login_name": {
      "label": "index.site_user.login_name",
      "type": "text",
      "sort": true,
      "quick": true,
      "search": true
    },
    "enabled": {
      "label": "index.site_user.enabled",
      "type": "checkbox",
      "batch": true
    },
    "created_at": {
      "label": "index.site_user.created_at",
      "type": "datetime-local",
      "sort": true
    },
    "updated_at": {
      "label": "index.site_user.updated_at",
      "type": "datetime-local",
      "sort": true
    }
  },
  "extra": [],
  "action": [
    {
      "name": "seq",
      "roles": ["ROLE_SITEUSER_WRITE"]
      "component": require 'components/backendbase/partial/list/table/actions/seq.vue'
    },
    {
      "name": "view",
      "label": "action.view",
      "roles": ["ROLE_SITEUSER_WRITE"],
      "route": 'siteuser-detail'
    },
    {
      "name": "edit",
      "label": "action.edit",
      "roles": ["ROLE_SITEUSER_WRITE"],
      "route": 'siteuser-edit'
    },
    {
      "name": "quick",
      "roles": ["ROLE_SITEUSER_WRITE"]
      "component": require 'components/backendbase/partial/list/table/actions/quick.vue'
    },
    {
      "name": "delete",
      "roles": ["ROLE_SITEUSER_WRITE"],
      "component": require 'components/backendbase/partial/list/table/actions/delete.vue'
    }
  ]
}
```

資料來源格式(API回傳的資料範例)

* pages: 資料總頁數
* rows: 資料總筆數

```json
{
  "data": [
    {
      "id": "15677156",
      "username": "user1",
      "email": "user1@gmail.com",
      "status": true,
      "lastLogin": "2016-11-11T00:00:00",
      "createTime": "2016-12-08T00:00:00"
    },
    {
      "id": "46781266",
      "username": "user2",
      "email": "user2@gmail.com",
      "status": true,
      "lastLogin": "2016-05-11T00:00:00",
      "createTime": "2016-03-06T00:00:00"
    },
    {
      "id": "26478156",
      "username": "user3",
      "email": "user3@gmail.com",
      "status": false,
      "lastLogin": "2016-10-11T00:00:00",
      "createTime": "2016-12-11T00:00:00"
    }
  ],
  "pager": {
    "page": 1,
    "pages": 4,
    "rows": 32
  }
}
```

## 加入router

路徑: cm4/src/BaseBundle/Resources/vue/router.json

```json
[
  {
    "path": "/siteuser",
    "name": "siteuser-list",
    "component": "components/backendbase/router/list/siteuser.vue"
  }
]

```

## 加入menu

路徑: cm4/src/BaseBundle/Resources/vue/menu.json

```json
{
  "帳號列表": {
    "icon": "fa fa-user",
    "role": [
      "ROLE_TEST"
    ],
    "route": {
      "帳號列表": {
        "icon": "fa fa-user",
        "route": "siteuser-list",
        "role": [
          "ROLE_TEST2"
        ]
      }
    }
  }
}
```

## bulid router及menu

**sf dgfactor:assets:install**


## 其他

**api請求篩選條件**

* search: 搜尋條件
* sort: 排序
* page: 頁數

```json
{ 
	"search": { 
		"username": "user1", 
		"email": "user1@gmail.com" 
	}, 
	"sort": { 
		"id": "desc",
		"username": "asc",
		"email": "desc"
	}, 
	"page": 1 
}
```

**設定快速搜尋按鈕**

在extra內載入一個name為"quick_search"的元件

config

* key為該資料的key, 例如商品上下架狀態為"status_shelf"

* status為狀態
	* label: 條件顯示名稱
	* apiName: api的名稱(需同components/actions/api資料夾內coffee的api名稱)
	* value: 條件值(all為刪除所有搜尋條件)
	
```coffeescript
"extra": [
    {
      "name": "quick_search",
      "label": "action.quick",
      "roles": ["ROLE_PRODUCT_WRITE"]
      component: require 'components/backendbase/partial/list/extra/quicksearch.vue'
      config:
        key: 'status_shelf'
	apiName: 'product'
        status: [
          { label: 'index.product.quick_search.all', value: 'all' }
          { label: 'index.product.quick_search.on_shelf', value: true }
          { label: 'index.product.quick_search.off_shelf', value: false }
        ]
    }
]
```