abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class Edit extends abstractEdit
  _init: () ->
    require("components/widgetinvitation/actions/api/invitation.coffee") @api
  injectConfig: () ->
    require 'components/widgetinvitation/config/edit/invitation.coffee'
# 取得資料
  _getData: (id) ->
    @api.invitation.read id
# 儲存表單
  save: () ->
    @api.invitation.update @editSetting.dataRow.id, @editSetting.dataRow
  create: () ->
    @api.invitation.create @editSetting.dataRow
module.exports = (api, id) ->
  new Edit(api, id)
