module.exports =
  mixins: [
    require "components/backendbase/mixins/base.coffee"
  ]
  computed:
    dataId: () ->
      @$route.params.id
  watch:
    "$route": () ->
      @$store.dispatch 'view.initModel', @getModel()(@$store.state.base.api, @dataId)
      @updateTitle()
      @routerChange() if @routerChange != undefined
      return
  created: () ->
    @$store.dispatch 'view.initModel', @getModel()(@$store.state.base.api, @dataId)
    @updateTitle()
  components:
    'component-view': require "components/backendbase/partial/view.vue"
  methods:
    updateTitle: () ->
      @dataId = @$route.params.id if @$route.params.id != undefined
      title = @$route.name.replace('-', '.')
      @$store.dispatch "pageTitle.update", title
      return