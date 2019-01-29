queryString = require 'qs'
module.exports = {
  data: () ->
    onChange: false
  computed:
    token:  () ->
      @$store.getters.token
    quickSuccess: () ->
      @$store.getters.quickSuccess
    quickError: () ->
      @$store.getters.quickError
    filterData: () ->
      @$store.getters.filterData
  beforeCreate: () ->
    sessionStorage.setItem('default_filter', true)
  created: () ->
    @$store.dispatch 'list.init.uniqueName', @$route.name
    @$store.dispatch "list.init.setting", @listConfig()(@$store.state.base.api)
    @["page.init"]()
    return
  components:
    'list': require "components/backendbase/partial/list.vue"
  methods:
    "page.init": () ->
      @updateTitle()
      @setFilter()
      @routerChange() if @routerChange != undefined
      return
    updateTitle: () ->
      @$store.dispatch "pageTitle.update", "menu.#{@modelName()}"
      return
    modelName: () ->
      @$route.name.split('-')[0]
    setPage: () ->
      page = 1
      if @$route.query.page != undefined
        page = @$route.query.page
      @$store.dispatch 'list.setPage', page
    setFilter: () ->
      filter = {}
      query = queryString.parse @$route.query
      filter.search = queryString.parse query.search
      filter.sort = queryString.parse query.sort
      filter.page = query.page
      filter.page = 1 if query.page == undefined
      @$store.dispatch 'list.inject.filter', filter
  watch:
    '$route': () ->
      @["page.init"]()
      @setPage()
      return
    quickSuccess:
      deep: true
      handler:(quickSuccess) ->
        if quickSuccess != ''
          successMsg = @$options.filters.trans "quick.success.#{@$route.name.replace('-', '.')}"
          @$store.dispatch 'alert',
            style: 'success'
            title: @$options.filters.trans 'list.alert.quick.success.title'
            message: successMsg
    quickError: (quickError) ->
      if quickError != ''
        errorMsg = ''
        for key of quickError
          errorMsg += "#{@$options.filters.trans quickError[key]}<br>"
        @$store.dispatch 'alert',
          style: 'error'
          title: @$options.filters.trans 'list.alert.quick.fail.title'
          message: errorMsg
      if quickError.msg == '403 Forbidden'
        errorMsg = @$options.filters.trans 'list.alert.quick.fail.role_not_allow.message'
        @$store.dispatch 'alert',
          style: 'error'
          title: @$options.filters.trans 'list.alert.quick.fail.title'
          message: errorMsg

    filterData:
      deep: true
      handler: (filter) ->
        me = @
        return if @onChange == true
        @onChange = true

        query = {}
        query.search = queryString.stringify(filter.search, { encode: false }) if queryString.stringify(filter.search).trim() != ''
        query.sort = queryString.stringify(filter.sort, { encode: false }) if queryString.stringify(filter.sort).trim() != ''
        query.page = filter.page
        @$router.push
          query: query

        # 避免重複綁定狂發api
        setTimeout () ->
          me.onChange = false
        , 1000
}