abstractList = require "components/backendbase/actions/abstract-list.coffee"

class SiteGroupList extends abstractList
  _init: () ->
    require('components/backendbase/actions/api/sitegroup.coffee') @api
  _getConfig: () ->
    require('components/backendbase/config/list/sitegroup.coffee')
# 資料請求
  request: () -> @api.sitegroup.search @_searchData()
# 批次處理
  batch: (ids, action, column) ->
    @api.sitegroup.batch ids, action, column

# 快速編輯, 切換狀態
  quickEdit: (id, data) -> @api.sitegroup.update id, data

# 刪除資料
  deleteData: (id) -> @api.sitegroup.delete id

module.exports = (api) ->
  new SiteGroupList(api)