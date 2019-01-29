class abstractView
  constructor: (@api, @dataId) ->
    config: []
    data: []
    @_init()
  _init: () ->
    return

  getConfig: () ->
    return @config

  initData: () ->
    me = @
    new Promise (resolve, reject) ->
      me._getData(me.dataId)
      .then (data) ->
        me.setData  data
        resolve data
        return
      .catch (error) ->
        reject error
        return

  _getData: () ->
    return

  setData: (data) ->
    @data = data
module.exports = abstractView