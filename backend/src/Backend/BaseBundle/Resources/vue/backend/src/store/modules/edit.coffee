editSettingObj = {}
module.exports =
  state:
    model: {}
    imageFormData: {}
    queueImages: []
    editSetting:
      config: {}
      dataRow: {}
    rerenderRepeatedForm: true
    disableSubmitButton: false
    onChange: false
  mutations:
    # 初始化設定
    'edit.initSetting': (state, config) ->
      state.editSetting.config = config
      state.imageFormData = {}

    # 初始化資料
    'edit.initData': (state, data) ->
      state.editSetting.dataRow = data

    # 同步資料
    'edit.syncData': (state, data) ->
      # 遞迴搜尋key, 找到key就同步資料
      dataKey = data.key

      if typeof state.editSetting.dataRow != 'object'
        state.editSetting.dataRow = {}
        console.log 'refactor'

      # 子表單
      if data.deep == true
        node = dataKey.split '.'
        if state.editSetting.dataRow[node[0]] == undefined
          state.editSetting.dataRow[node[0]] = {}
        state.editSetting.dataRow[node[0]][node[1]] = data.value
        delete state.editSetting.dataRow[dataKey]
      # 重複表單
      else if data.repeated == true
        state.editSetting.dataRow[data.parent][data.dataIndex][data.key] = data.value
      else
        state.editSetting.dataRow[data.key] = data.value

#      state.editSetting.dataRow[data.key] = data.value
      state.model.setData state.editSetting.dataRow
      state.onChange = true
      setTimeout () ->
        state.onChange = false
      , 0
    # 同步formData
    'edit.update.imageFormData': (state, data) ->
      state.imageFormData = data

    'edit.update.queueImages': (state, data) ->
      state.queueImages = data

    'edit.update.progress': (state, data) ->
      state.queueImages[data.id].progress = data.progress

    'edit.initModel': (state, model) ->
      state.model = model

    'edit.init.data': (state, data) ->
      state.editSetting.dataRow = data

    'edit.init.config': (state, data) ->
      state.editSetting.config = data

    'edit.update.photo': (state, data) ->
      if state.editSetting.dataRow[data.key].photos == undefined
        state.editSetting.dataRow[data.key].photos = []
      state.editSetting.dataRow[data.key].photos.push data.photo
      state.model.setData state.editSetting.dataRow


    'edit.delete.photo': (state, data) ->
      state.editSetting.dataRow[data.key].photos.splice data.index, 1
#      state.model.setData state.editSetting.dataRow
    'edit.create.repeatedForm': (state, data) ->
      state.rerenderRepeatedForm = false
      if data.mode == 'new'
        state.editSetting.dataRow[data.parent] = []
      emptyData = {}
      for config in data.configs
        if config.type == 'date'
          emptyData[config.name] = undefined
        else
          emptyData[config.name] = ''
      state.editSetting.dataRow[data.parent].push emptyData
      state.rerenderRepeatedForm = true

    'edit.delete.repeatedForm': (state, data) ->
      new Promise (resolve, reject) ->
        state.rerenderRepeatedForm = false
        state.editSetting.dataRow[data.parent].splice data.index, 1
        resolve()
      .then () ->
        state.rerenderRepeatedForm = true
    'edit.submitButton.active': (state) ->
      state.disableSubmitButton = false

    'edit.submitButton.disabled': (state) ->
      state.disableSubmitButton = true
  actions:
    'edit.initSetting': (context, config) ->
      context.commit 'edit.initSetting', config

    'edit.initData': (context, data) ->
      context.commit 'edit.initData', data

    'edit.syncData': (context, data) ->
      context.commit 'edit.syncData', data

    'edit.update.imageFormData': (context, data) ->
      context.commit 'edit.update.imageFormData', data

    'edit.update.queueImages': (context, data) ->
      context.commit 'edit.update.queueImages', data

    'edit.update.progress': (context, data) ->
      context.commit 'edit.update.progress', data

    'edit.initModel': (context, model) ->
      editSettingObj = model
      context.commit 'edit.initModel', model

      # 初始化設定
      context.commit 'loading.change', true
      context.commit 'edit.init.data', {}
      setConfigPromise = editSettingObj.setConfigCallback()
      setConfigPromise
      .then () ->
        context.commit 'edit.init.config', editSettingObj.getConfig()
        editSettingObj.initData()
      .then (result) ->
        if result != null
          context.commit 'edit.init.data', result
        context.commit 'loading.change', false
      .catch (err) ->
        context.commit 'loading.change', false
        context.commit 'alert',
          style: 'error'
          title: context.getters.trans '404.title'
          message: context.getters.trans '404.message'
    'edit.update.photo': (context, data) ->
      context.commit 'edit.update.photo', data
    'edit.delete.photo': (context, data) ->
      context.commit 'edit.delete.photo', data

    'edit.create.repeatedForm': (context, data) ->
      context.commit 'edit.create.repeatedForm', data
    'edit.delete.repeatedForm': (context, data) ->
      context.commit 'edit.delete.repeatedForm', data

    'edit.submitButton.active': (context) ->
      context.commit 'edit.submitButton.active'

    'edit.submitButton.disabled': (context) ->
      context.commit 'edit.submitButton.disabled'
  getters:
    'editModel': (state) ->
      return state.model

    'editSetting': (state) ->
      return state.editSetting

    'imageFormData': (state) ->
      return state.imageFormData

    'queueImages': (state) ->
      return state.queueImages

    'rerenderRepeatedForm': (state) ->
      return state.rerenderRepeatedForm

    'formOnChange': (state) ->
      return state.onChange

    disableSubmitButton: (state) ->
      return state.disableSubmitButton