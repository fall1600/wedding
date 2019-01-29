viewObj = {}
module.exports =
  state:
    config: []
    data: []
  mutations:
    'view.init.data': (state, data) ->
      state.data = data
    'view.init.config': (state, data) ->
      state.config = data
  actions:
    'view.initModel': (context, model) ->
      viewObj = model

      context.commit 'loading.change', true
      context.commit 'view.init.data', {}
      context.commit 'view.init.config', viewObj.getConfig()
      viewObj.initData()
      .then (result) ->
        if result != null
          context.commit 'view.init.data', result
        context.commit 'loading.change', false
      .catch (err) ->
        context.commit 'loading.change', false
        context.commit 'alert',
          style: 'error'
          title: context.getters.trans '404.title'
          message: context.getters.trans '404.message'
  getters:
    view: (state) ->
      state