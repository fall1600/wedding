abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class SiteGroupEdit extends abstractEdit
  _init: () ->
    require("components/backendbase/actions/api/sitegroup.coffee") @api
  injectConfig: () ->
    require('components/backendbase/config/edit/sitegroup.coffee')
# 取得資料
  _getData: (id) ->
    @api.sitegroup.read id
# 儲存表單
  save: () ->
    @api.sitegroup.update @editSetting.dataRow.id, @editSetting.dataRow
    @api.sitegroup.setSiteUsers @editSetting.dataRow.id, @editSetting.dataRow
  create: () ->
    me = @
    promise = @api.sitegroup.create @editSetting.dataRow
    promise.then (resolve) ->
      me.api.sitegroup.setSiteUsers resolve.id, me.editSetting.dataRow
module.exports = (api, id) ->
  new SiteGroupEdit(api, id)