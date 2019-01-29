module.exports =
  state:
    mixins: []
    styleMixinRender: true
  mutations:
    'style.init.mixins': (state, data) ->
      state.styleMixinRender = false
      state.mixins = data
      setTimeout () ->
        state.styleMixinRender = true
      , 0
    'style.init.empty.mixins': (state, amount) ->
      state.styleMixinRender = false
      state.mixins = []
      for index in [0..amount-1]
        style =
          label: ''
          attrs: []
        state.mixins.push style
      setTimeout () ->
        state.styleMixinRender = true
      , 0
    'style.sync.mixins': (state, data) ->
      state.mixins[data.index][data.colum] = data.data
    'style.increment.mixins': (state) ->
      style =
        label: ''
        attrs: []
      state.mixins.push style
    'style.delete.mixins': (state, index) ->
      state.styleMixinRender = false
      state.mixins.splice index, 1
      setTimeout () ->
        state.styleMixinRender = true
      , 0
  actions:
    'style.init.mixins': (context, data) ->
      context.commit 'style.init.mixins', data
    'style.init.empty.mixins': (context, amount) ->
      context.commit 'style.init.empty.mixins', amount
    'style.sync.mixins': (context, data) ->
      context.commit 'style.sync.mixins', data
    'style.increment.mixins': (context) ->
      context.commit 'style.increment.mixins'
    'style.delete.mixins': (context, index) ->
      context.commit 'style.delete.mixins', index
  getters:
    styleMixin: (state) ->
      state.mixins
    styleMixinRender: (state) ->
      state.styleMixinRender