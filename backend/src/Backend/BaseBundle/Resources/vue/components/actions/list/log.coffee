abstractList = require "components/backendbase/actions/abstract-list.coffee"

class LogList extends abstractList
  _init: () ->
    require('components/backendbase/actions/api/log.coffee') @api
  _getConfig: () ->
    require('components/backendbase/config/list/log.coffee')
# 資料請求
  request: () -> @api.log.search @_searchData()
# 批次處理
  batch: (ids, action, column) ->
    @api.log.batch ids, action, column

# 快速編輯, 切換狀態
  quickEdit: (id, data) -> @api.log.update id, data

# 刪除資料
  deleteData: (id) -> @api.log.delete id

module.exports = (api) ->
  new LogList(api)