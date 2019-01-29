abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class PhotoConfigEdit extends abstractEdit
  _init: () ->
    require("components/widgetphoto/actions/api/photoconfig.coffee") @api
  injectConfig: () ->
    require 'components/widgetphoto/config/edit/photoconfig.coffee'
# 取得資料
  _getData: (id) ->
    @api.photoconfig.read id
# 儲存表單
  save: () ->
    @api.photoconfig.update @editSetting.dataRow.id, @editSetting.dataRow
  create: () ->
    @api.photoconfig.create @editSetting.dataRow
module.exports = (api, id) ->
  new PhotoConfigEdit(api, id)