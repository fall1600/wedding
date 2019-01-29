abstractList = require "components/backendbase/actions/abstract-list.coffee"

class SiteUserList extends abstractList
  _init: () ->
    require('components/backendbase/actions/api/siteuser.coffee') @api
  _getConfig: () ->
    require('components/backendbase/config/list/siteuser.coffee')
# 資料請求
  request: () -> @api.siteuser.search @_searchData()
# 批次處理
  batch: (ids, action, column, value) ->
    @api.siteuser.batch ids, action, column, value

# 快速編輯, 切換狀態
  quickEdit: (id, data) -> @api.siteuser.update id, data

# 刪除資料
  deleteData: (id) -> @api.siteuser.delete id

module.exports = (api) ->
  new SiteUserList(api)