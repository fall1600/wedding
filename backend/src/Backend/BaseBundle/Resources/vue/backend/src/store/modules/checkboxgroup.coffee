module.exports =
  state:
    checkboxGroup: []
  mutations:
    'checkboxgroup.update.list': (state, data) ->
      state.checkboxGroup = []
      state.checkboxGroup = data
  actions:
    'checkboxgroup.update.list': (context, data) ->
      context.commit 'checkboxgroup.update.list', data
  getters:
    checkboxGroup: (state) ->
      state.checkboxGroup