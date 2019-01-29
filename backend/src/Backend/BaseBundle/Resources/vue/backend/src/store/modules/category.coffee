categoryModel = {}
module.exports =
  state:
    config: {}
    data: {}
    startRender: false
    edit: {}
    mutateFinished: false
    responseId: ''
  mutations:
    'category.init.data': (state, data) ->
      state.mutateFinished = false
      state.startRender = false
      state.data = {}
      if data.name == ''
        data.name = '根目錄'
      state.data[data.id] = data
      state.startRender = true
      state.mutateFinished = true
    'category.init.config': (state, config) ->
      state.config = config
    'category.update.data': (state, data) ->
      if data.children.length != 0
        state.mutateFinished = false
        childrenArray = []
        for child in data.children
          state.data[child.id] = child
          state.data[child.id].parent = data.id
          childrenArray.push state.data[child.id]
        if state.data[data.id] != undefined
          state.data[data.id]['children'] = childrenArray
        state.mutateFinished = true
    'category.update.edit': (state, data) ->
      state.edit = data
    'category.delete.node': (state, id) ->
      state.mutateFinished = false
      delete state.data[id]

      # 使用遞迴刪除tree
      searchTree = (obj, parent) ->
        return if obj == undefined
        if obj.id == id || obj.id == id.toString()
          for item, index in parent
            do (item, index) ->
              return if item == undefined
              if item.id == id || item.id == id.toString()
                parent.splice index, 1
                state.mutateFinished = true
        else
          return if obj.children == undefined
          for child in obj.children
            do (child) ->
              searchTree child, obj.children

      for nodeId of state.data
        if state.data[nodeId].parent == undefined
          tree = state.data[nodeId]
      searchTree tree
      state.mutateFinished = true

    # 將新增成功的資料, 加入tree中
    'category.insert.newData': (state, dataToInsert) ->
      state.mutateFinished = false
      state.data[dataToInsert.data.id] = dataToInsert.data
      state.data[dataToInsert.data.id].parent = dataToInsert.parent
      if state.data[dataToInsert.parent].children != undefined
        state.data[dataToInsert.parent].children.push dataToInsert.data
      state.mutateFinished = true
    # 將更新成功的資料, 更新到tree中
    'category.update.node': (state, data) ->
      state.mutateFinished = false
      for key of data
        state.data[data.id][key] = data[key]
      state.mutateFinished = true

    # 改變mutateFinished讓tree reload
    'category.reload.tree': (state) ->
      state.mutateFinished = false
      state.mutateFinished = true

    'category.update,responseId': (state, id) ->
      state.responseId = id
  actions:
    # model初始化
    'category.initModel': (context, model) ->
      categoryModel = model
      context.commit 'category.init.config', categoryModel.getConfig()
      promise = categoryModel.getRoot()
      promise.then (result) ->
        # 初始化data
        context.commit 'category.init.data', result
        context.dispatch 'category.getChildren', result.id

    # 取得子分類, 加入store
    'category.getChildren': (context, id) ->
      promise = categoryModel.getChildren(id)
      promise.then (children) ->
        data =
          id: id
          children: children
        context.commit 'category.update.data', data

    'category.move': (context, data) ->
      if data.position == 'inside'
        promise = categoryModel.moveInside(data.move, data.target)
      if data.position == 'after'
        promise = categoryModel.moveAfter(data.move, data.target)
      promise.then () ->
        context.dispatch 'alert',
          style: 'success'
          title: '移動成功'
          message: '移動成功'
      .catch () ->
        context.commit 'category.reload.tree'
        context.dispatch 'alert',
          style: 'error'
          title: '移動失敗'
          message: '移動失敗'

    'category.delete': (context, id) ->
#      context.commit 'category.delete.node', id
      promise = categoryModel.delete(id)
      promise.then () ->
        context.commit 'category.delete.node', id
        context.dispatch 'alert',
          style: 'success'
          title: '刪除成功'
          message: '刪除成功'
      promise.catch (error) ->
        context.commit 'category.reload.tree'
        context.dispatch 'alert',
          style: 'error'
          title: '刪除失敗'
          message: '刪除失敗'
    'category.reload.tree': (context) ->
      context.commit 'category.reload.tree'

    # 啟動編輯(新增/更新), 設定store上的編輯狀態
    'category.edit': (context, data) ->
      context.commit 'category.update.edit', data

    'category.save': (context, data) ->
      context.commit 'category.update,responseId', ''
      # 新增
      if data.id == null
        promise = categoryModel.create(data)
      # 更新
      else
        promise = categoryModel.update(data)
      promise.then (result) ->
        context.commit 'category.update,responseId', result.id
        if data.id == null
          # 組出新的資料, 寫入store
          dataToInsert =
            parent: data.target
            data: data
          dataToInsert.data.id = result.id
          delete dataToInsert.data.target
          context.commit 'category.insert.newData', dataToInsert
          context.dispatch 'category.getChildren', dataToInsert.parent
        else
          delete data.target
          context.commit 'category.update.node', data
        context.dispatch 'alert',
          style: 'success'
          title: '儲存成功'
          message: '儲存成功'
      .catch (error) ->
        context.commit 'category.reload.tree'
        context.dispatch 'alert',
          style: 'error'
          title: '儲存失敗'
          message: '儲存失敗'
  getters:
    'categoryMutateFinished': (state) ->
      return state.mutateFinished
    'categoryConfig': (state) ->
      return state.config
    'categoryData': (state) ->
      return state.data
    'categoryEdit': (state) ->
      return state.edit
    'categoryStartRender': (state) ->
      return state.startRender
    categoryResponseId: (state) ->
      return state.responseId