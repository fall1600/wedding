module.exports =
  created: () ->
    @$store.dispatch 'category.initModel', @getModel()(@$store.state.base.api)
    @$store.dispatch "pageTitle.update", "#{@$route.name.replace('-', '.').replace('-', '.')}"
  components:
    'category': require 'components/backendbase/partial/category.vue'
