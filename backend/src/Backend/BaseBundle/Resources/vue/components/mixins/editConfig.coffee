module.exports =
  mixins: [
    require "components/backendbase/mixins/base.coffee"
  ]
  data: () ->
    {
      dataId: null
    }
  watch:
    "$route": () ->
      @["form.resetDataId"]()
      @$store.dispatch 'edit.initModel', @getModel()(@$store.state.base.api, @dataId)
      @updateTitle()
      @routerChange() if @routerChange != undefined
      return
  created: () ->
    @["form.resetDataId"]()
    @$root.$on 'save-edit', @['form.save']
    @$root.$on 'form.postSaveSuccess', @['form.postSaveSuccess']
    @$root.$on 'form.postSaveFail', @['form.postSaveFail']
    @$store.dispatch 'edit.initModel', @getModel()(@$store.state.base.api, @dataId)
    @updateTitle()
    return
  beforeDestroy: () ->
    @$root.$off 'save-edit', @['form.save']
    @$root.$off 'form.postSaveSuccess', @['form.postSaveSuccess']
    @$root.$off 'form.postSaveFail', @['form.postSaveFail']
    return
  components:
    'edit': require "components/backendbase/partial/edit.vue"
  methods:
    modelName: () ->
      @$route.name.split('-')[0]
    updateTitle: () ->
      if @isNew()
        status = 'create'
      else
        status = 'update'
      @$store.dispatch "pageTitle.update", "#{@modelName()}.#{status}"
      return
    isNew: () ->
      @$route.name.split('-')[1] == 'new'
    "form.resetDataId": () ->
      @dataId = null
      @dataId = @$route.params.id if @$route.params.id != undefined
      return
    "form.postSaveSuccess": (result, redirect) ->
      @$store.dispatch 'alert',
        style: 'info'
        title: '儲存成功'
        message: '儲存成功'
      @$root.$emit 'form.showError', {}

      if redirect == null || redirect == undefined
        route = "#{@modelName()}-edit"
        @$router.push
          name: route
          params:
            id: result.id
      else
        if @redirect != undefined && @redirect != null
          @$router.push @redirect
          return
        @$router.go -1
#        route = "#{@modelName()}-#{redirect}"
#        history = JSON.parse sessionStorage.getItem('filter')
#        filter =
#          page: 1
#        filter = @history.getFilterQuery(route) if @history.getFilterQuery(route) != null
#        @$router.push
#          name: route
#          query: filter
    "form.postSaveFail": (error) ->
      status = error[0].status
      responseJson = error[0].responseJSON
      switch status
        when 401
          @$store.dispatch 'alert',
            style: 'error'
            title: '儲存失敗'
            message: '系統已登出, 請重新登入'
          break
        when 500
          @$store.dispatch 'alert',
            style: 'error'
            title: '儲存失敗'
            message: '伺服器錯誤, 錯誤代碼500'
          break
        else
          @$store.dispatch 'alert',
            style: 'error'
            title: '儲存失敗'
            message: "請檢查表單"
          @$root.$emit 'form.showError', responseJson
      @$store.dispatch 'edit.submitButton.active'
    "form.save": (redirect) ->
      me = @
      # 存表單前處理事件
      result =
        promises: []
      @$root.$emit 'form.beforeSave', result
      @$store.dispatch 'edit.submitButton.disabled'

      beforeSavePromise = new Promise (resolve, reject) ->
        Promise.all(result.promises).then () ->
          resolve()
        .catch () ->
          me.$store.dispatch 'edit.submitButton.active'

      beforeSavePromise
      .then (result) ->
        return me.editModel.create() if me.isNew()
        me.editModel.save()
      .then (result) ->
        me.dataId = result.id
#        me.$root.$emit 'form.postSaveSuccess', result, redirect
        me['form.postSaveSuccess'](result, redirect)
        me.$store.dispatch 'edit.submitButton.active'
      .catch (error) ->
        me.$store.dispatch 'edit.submitButton.active'
        me.$root.$emit 'form.postSaveFail', error

  computed:
    history: () -> @$store.getters.history
    editModel: () ->
      return @$store.getters.editModel
    editSetting: () ->
      return @$store.getters.editSetting
    imageFormData: () ->
      return @$store.getters.imageFormData
