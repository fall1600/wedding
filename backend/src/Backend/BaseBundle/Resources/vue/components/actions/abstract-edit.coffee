class abstractEdit
  constructor: (@api, @dataId) ->
    @editSetting =
      config: []
      dataRow: {}
    @_init()
  _init: () ->
    return
# 取得callback資料
  each: (configs, cb) ->
    for config, index in configs
      if config.config != undefined && config.config.api != undefined
        cb index, config

      # 取得子表單資料
      if config.type == 'subform'
        for subform, subformIndex in config.config
          for subformConfig, subformConfigIndex in subform.content
            if subformConfig.config != undefined && subformConfig.config.api != undefined
              subformConfig.isSubform = true
              cb index, subformConfig, subformIndex, subformConfigIndex
      # 取得可複製表單資料
      if config.type == 'repeatedform'
        for repeatedformConfig, repeatedformIndex in config.config
          if repeatedformConfig.config != undefined && repeatedformConfig.config.api != undefined
            repeatedformConfig.isReapetedForm = true
            cb index, repeatedformConfig, config.name, repeatedformIndex
      if config.dynamicCreated == true
        cb index, config
    return
  setConfigCallback: () ->
    me = @
    configs = @injectConfig()
    new Promise (mainResolve, mainReject) ->
      promiseList = []

      me.each configs, (index, config) ->
        api = me.api

        for apiName in config.config.api.split('.')
          api = api[apiName]
        promiseList.push api()

      #      全部callback回來後, 將callback資料更新到config中
      Promise.all promiseList
      .then (result) ->
        me.each configs, (index, config, subformIndex, subformConfigIndex) ->
          # 將callback資料寫入
          if config.isSubform == true
            # 子表單
            configs[index].config[subformIndex].content[subformConfigIndex].data = result.shift()

          else if config.isReapetedForm == true
            # 可複製表單
            configs[index].config[subformConfigIndex].data = result.shift()
          else
            # 一般表單
            configs[index].data = result.shift()
        me.editSetting.config = configs
        mainResolve()
      .catch (error) ->
        mainReject(error)
  getConfig: () ->
    return @editSetting.config
# 取得資料
  getData: () ->
    return @editSetting.dataRow

# 初始化config
  initConfig: () ->
    @editSetting.config = @getConfig(@editSetting.dataRow)

# 初始化資料
  initData: () ->
    me = @
    if @dataId == null
      return new Promise (resolve, reject) ->
        me.setData {}
        resolve {}
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
# 設定資料
  setData: (data) ->
    @editSetting.dataRow = data
# 同步資料
  syncData: (data) ->
    @editSetting.dataRow[data.key] = data.value
  save: () ->
    return
  create: () ->
    return
module.exports = abstractEdit