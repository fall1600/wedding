module.exports =
  state:
    config: []
  mutations:
    'modal.show': (state, config) ->
      state.config = config
  actions:
    'modal.show': (context, config) ->
      context.commit 'modal.show', config
  getters:
    modalConfig: (state) ->
      return state.config
