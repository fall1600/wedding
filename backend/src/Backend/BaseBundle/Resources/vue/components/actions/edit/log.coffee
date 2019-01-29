abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class LogEdit extends abstractEdit
  _init: () ->
    require("components/backendbase/actions/api/log.coffee") @api
  injectConfig: () ->
    require('components/backendbase/config/edit/log.coffee')
# 取得資料
  _getData: (id) ->
    @api.log.read id
# 儲存表單
  save: () ->
    @api.log.update @editSetting.dataRow.id, @editSetting.dataRow
  create: () ->
    @api.log.create @editSetting.dataRow
module.exports = (api, id) ->
  new LogEdit(api, id)