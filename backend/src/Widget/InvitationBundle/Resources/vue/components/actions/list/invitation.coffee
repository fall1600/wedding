abstractList = require "components/backendbase/actions/abstract-list.coffee"

class List extends abstractList
  _init: () ->
    require('components/widgetinvitation/actions/api/invitation.coffee') @api
  _getConfig: () ->
    require('components/widgetinvitation/config/list/invitation.coffee')
# 資料請求
  request: () -> @api.invitation.search @_searchData()

# 刪除資料
  deleteData: (id) -> @api.invitation.delete id

module.exports = (api) ->
  new List(api)
