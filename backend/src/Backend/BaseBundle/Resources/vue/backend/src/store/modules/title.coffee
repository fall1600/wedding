module.exports =
  state:
    pageTitle: ''
  mutations:
    'pageTitle.update': (state, title) ->
      state.pageTitle = title
  actions:
    'pageTitle.update': (context, title) ->
      context.commit "pageTitle.update", title
      return
  getters:
    "pageTitle": (state) ->
      state.pageTitle
