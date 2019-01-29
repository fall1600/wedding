abstractList = require "components/backendbase/actions/abstract-list.coffee"

class PhotoConfigList extends abstractList
  _init: () ->
    require('components/widgetphoto/actions/api/photoconfig.coffee') @api
  _getConfig: () ->
#    config
    require('components/widgetphoto/config/list/photoconfig.coffee')
# 資料請求
  request: () -> @api.photoconfig.search @_searchData()
# 批次處理
  batch: (ids, action, column) ->
    @api.photoconfig.batch ids, action, column

# 快速編輯, 切換狀態
  quickEdit: (id, data) -> @api.photoconfig.update id, data

# 刪除資料
  deleteData: (id) -> @api.photoconfig.delete id

module.exports = (api) ->
  new PhotoConfigList(api)
