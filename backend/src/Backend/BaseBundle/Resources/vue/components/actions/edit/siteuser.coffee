abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class SiteUserEdit extends abstractEdit
  _init: () ->
    require("components/backendbase/actions/api/siteuser.coffee") @api
  injectConfig: () ->
    require('components/backendbase/config/edit/siteuser.coffee')
# 取得資料
  _getData: (id) ->
    @api.siteuser.read id
# 儲存表單
  save: () ->
    @api.siteuser.update @editSetting.dataRow.id, @editSetting.dataRow
    @api.siteuser.setSiteGroups @editSetting.dataRow.id,  @editSetting.dataRow
  create: () ->
    me = @
    promise = @api.siteuser.create @editSetting.dataRow
    promise.then (resolve) ->
      me.api.siteuser.setSiteGroups resolve.id,  me.editSetting.dataRow
module.exports = (api, id) ->
  new SiteUserEdit(api, id)