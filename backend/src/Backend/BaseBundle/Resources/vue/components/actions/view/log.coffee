abstractView = require "components/backendbase/actions/abstract-view.coffee"

class LogView extends abstractView
  _init: () ->
    require("components/backendbase/actions/api/log.coffee") @api
  getConfig: () ->
    require('components/backendbase/config/view/log.coffee')
# 取得資料
  _getData: (id) ->
    @api.log.read id

module.exports = (api, id) ->
  new LogView(api, id)