module.exports =
  state:
    dynamicMenu: {}
  mutations:
    'menu.update.dynamic': (state, config) ->
      state.dynamicMenu[config.key] = config.isShow
  actions:
    'menu.update.dynamic': (context, config) ->
      context.commit 'menu.update.dynamic', config
  getters:
    dynamicMenu: (state) ->
      state.dynamicMenu