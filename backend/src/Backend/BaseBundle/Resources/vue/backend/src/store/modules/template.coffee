export default {
  state:
    defaultStyle: true
    templates: []
    styles: []
    toAdd: []
    toDelete: []
    customAttrGroups: null
  mutations:
    'template.init': (state, data) ->
      state.templates = data
    'template.checked.update': (state, data) ->
      state.templates[data.index].checked = data.checked
      return if data.checked == false
      state.defaultStyle = false
    'template.sync.defaulStyle': (state, data) ->
      state.defaultStyle = data
      return if data == false
      for template in state.templates
        template.checked = false
    'template.sync.toAdd': (state, data) ->
      array = []
      for item in data
        if item.trim() != ''
          styleArray = []
          for style in item.split('-')
            styleArray.push style
          array.push styleArray
      state.toAdd = array
    'template.sync.toDelete': (state, data) ->
      state.toDelete = data
    'style.init': (state, data) ->
      for item in data
        item.origin_name = item.name
      state.styles = data
    'style.sync.customAttrGroups': (state, data) ->
      state.customAttrGroups = data
  actions:
    'template.init': (context, data) ->
      context.commit 'template.init', data
    'template.checked.update': (context, data) ->
      context.commit 'template.checked.update', data
    'template.sync.defaulStyle': (context, data) ->
      context.commit 'template.sync.defaulStyle', data
    'template.sync.toAdd': (context, data) ->
      context.commit 'template.sync.toAdd', data
    'template.sync.toDelete': (context, data) ->
      context.commit 'template.sync.toDelete', data
    'style.init': (context, data) ->
      context.commit 'style.init', data
    'style.sync.customAttrGroups': (context, data) ->
      context.commit 'style.sync.customAttrGroups', data
  getters:
    templates: (state) ->
      state.templates
    defaultStyle: (state) ->
      state.defaultStyle
    templateToAdd: (state) ->
      state.toAdd
    templateToDelete: (state) ->
      state.toDelete
    styles: (state) ->
      state.styles
    customAttrGroups: (state) ->
      state.customAttrGroups
}