abstractEdit = require "components/backendbase/actions/abstract-edit.coffee"

class SiteConfigEdit extends abstractEdit
  _init: () ->
    require("components/backendbase/actions/api/site_config/system.coffee") @api
  injectConfig: () ->
    require 'components/backendbase/config/site_config/system.coffee'
# 取得資料
  _getData: () ->
    @api.siteConfig.read()
# 儲存表單
  save: () ->
    @api.siteConfig.update @editSetting.dataRow
module.exports = (api) ->
  new SiteConfigEdit(api)