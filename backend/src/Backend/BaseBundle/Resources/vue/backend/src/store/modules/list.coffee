listSettingObj = {}
module.exports =
  state:
    listData: []
    listConfig: []
    checkStatus: []
    listGroupAction: []
    actionButton: []
    filterData: []
    pager: []
    searchStatus: []
    quickSuccess: {}
    quickError: {}
    uniqueName: ''

  mutations:

    # 同步資料
    'list.syncData': (state, data) ->
      state.listData[data.row][data.key] = data.value

    # 初始化列表設定
    'list.init.setting': (state, listSetting) ->
      # 清除資料, search狀態, checkbox狀態
      state.listData = []
      state.searchStatus = []
      state.checkStatus = []

      state.listConfig = listSettingObj.listConfig
      state.actionButton = listSettingObj.listConfig.action
      state.search = {}

      # 初始化search狀態
      state.searchStatus = []
      for index in [0..state.listConfig.search.length-1]
        searchKey = state.listConfig.search[index]
        statusObj = {}
        statusObj.key = searchKey
        statusObj.type = state.listConfig.type[searchKey]
        statusObj.used = false
        state.searchStatus.push statusObj

    # 初始化列表資料
    'list.init.data': (state, dataSource) ->
      state.listData = dataSource
      state.pager = listSettingObj.pager

    # 初始化checkbox狀態, 全部設定為false
    'list.init.checkstatus': (state) ->
      state.checkStatus = []

      for dataIndex in [0..state.listData.length-1]
        checkstatusObj = {}
        checkstatusObj.status = false
        state.checkStatus.push checkstatusObj

    # 更新filter資料
    'list.update.filter': (state) ->
      state.filterData = listSettingObj.getFilterData()
      filter = listSettingObj.getFilterData()

      # filter history寫入sessionStore
      filterResult = JSON.parse(sessionStorage.getItem('filter'))
      filterResult = {} if filterResult == null
      filterResult[state.uniqueName] = listSettingObj.getFilterData()
      sessionStorage.setItem('filter', JSON.stringify(filterResult))

      # 更新搜尋列
      for key of listSettingObj.getFilterData().search
        for searchConfigKey of state.listConfig.searchConfig
          if state.listConfig.searchConfig[searchConfigKey] != undefined
            if state.listConfig.searchConfig[searchConfigKey].key == key
              for search, index in state.searchStatus
                if search.key == searchConfigKey
                  state.searchStatus[index].used = true
          for search, index in state.searchStatus
            if search.key == key
              state.searchStatus[index].used = true
      
    'list.clear.default.sort': (state) ->
      # 第一次進入頁面後, 移除預設sort
      default_filter = sessionStorage.getItem('default_filter')
      default_filter = true if default_filter == 'true'
      default_filter = false if default_filter == 'false'
      filter = listSettingObj.getFilterData()
      if default_filter == true
        sessionStorage.setItem('default_filter', JSON.stringify(false))
        for key of state.listConfig.defaultSort
          listSettingObj.clearSort(key)

    # 同步store的checkbox狀態
    'list.syncCheckStatus':(state, data) ->
      state.checkStatus[data.row].status = data.status

    # 同步search checkbox狀態
    'list.syncSearchStatus': (state, data) ->
      state.searchStatus[data.index].used = data.used

    # 依照id更新一筆資料
    'list.updateSingleData': (state, result) ->
      for singleData, index in state.listData
        if singleData.id == result.id
          state.listData[index] = result

    'list.update.quickSuccess': (state, result) ->
      state.quickSuccess = ''
      state.quickSuccess = result
    'list.update.quickError': (state, quickError) ->
      state.quickError = ''
      state.quickError = quickError

    'list.init.uniqueName': (state, uniqueName) ->
      state.uniqueName = uniqueName

    'list.keep.history': (state, config) ->
      history = JSON.parse sessionStorage.getItem('history')
      history = {} if history == undefined || history == null
      history[config.route] = {} if history[config.route] == undefined || history[config.route] == null
      history[config.route] = config.url
      sessionStorage.setItem('history', JSON.stringify(history))
  actions:

    # 同步資料
    'list.syncData': (context, data) ->
      context.commit 'list.syncData', data

    # 初始化設定
    'list.init.setting': (context, listSetting) ->
      listSettingObj = listSetting
      context.commit 'list.init.setting', listSetting
#      context.commit 'list.init.filter'
#      context.dispatch 'list.init.data'

    # 初始化資料
    'list.init.data': (context) ->
      return if context.getters.loading
      context.commit 'loading.change', true
      promise = listSettingObj.requestData()
      promise
      .then () ->
        context.commit 'loading.change', false
        context.commit 'list.init.data', listSettingObj.getListData()
        context.commit 'list.init.checkstatus'
        return
      .catch (data) ->
        context.commit 'loading.change', false
        return

    # 更新排序
    'list.updateOrder': (context, setSort) ->
      listSettingObj.setSort(setSort)
      context.commit 'list.update.filter'
      context.commit 'list.clear.default.sort'

      # 初始化資料
      context.dispatch 'list.init.data'

    # 切換分頁
    'list.setPage': (context, page) ->
      listSettingObj.setPage(page)
      context.commit 'list.update.filter'

      # 初始化資料
      context.dispatch 'list.init.data'

    # 同步checkbox狀態
    'list.syncCheckStatus':(context, data) ->
      context.commit 'list.syncCheckStatus', data

    # 批次管理
    'list.manage.group': (context, data) ->
      if data.ids.length == 0
        context.dispatch 'alert',
          style: 'error'
          title: '操作錯誤'
          message: '請先選取資料'
        return
      promise = listSettingObj.batch data.ids, data.action, data.colum, data.value
      promise.then (results) ->
        context.dispatch 'list.init.data'
        window.rootComponent.$root.$emit 'list.switch.success'
        context.dispatch 'alert',
          style: 'success'
          title: '操作成功'
          message: "資料操作成功"
        return
      .catch (reason) ->
        responseError = JSON.parse reason[0].responseText
        context.commit 'list.update.quickError', responseError
      return
    # 快速編輯, 切換狀態
    'list.quickEdit': (context, data) ->
      me = @
      promise = listSettingObj.quickEdit data.id, data.data
      promise.then (result) ->
#        context.dispatch 'list.init.data'
        context.commit 'list.update.quickSuccess', result
        window.rootComponent.$root.$emit 'list.switch.success'
      .catch (reason) ->
        responseError = JSON.parse reason[0].responseText
        context.commit 'list.update.quickError', responseError
        context.dispatch 'list.init.data'
        window.rootComponent.$root.$emit 'list.switch.fail'

    # 切換狀態
    'list.changeDataStatus': (context, data) ->
      listSettingObj.changeDataStatus data.id, data.value

    # 刪除資料
    'list.deleteData': (context, id) ->
      promise = listSettingObj.deleteData id
      promise.then () ->
        # 初始化資料
        context.dispatch 'list.init.data'
        window.rootComponent.$root.$emit 'list.switch.success'

        context.dispatch 'alert',
          style: 'success'
          title: '刪除成功'
          message: "刪除成功"
      .catch (reason) ->
        context.dispatch 'alert',
          style: 'error'
          title: '刪除失敗'
          message: '刪除失敗'

    # 更動排序
    'list.changeSeq': (context, data) ->
      promise = listSettingObj.changeSeq data.id, data.move
      promise.then (result) ->
        context.dispatch 'alert',
          style: 'success'
          title: '操作成功'
          message: "更動排序成功"

        context.dispatch 'list.init.data'
      .catch () ->
        context.dispatch 'alert',
          style: 'error'
          title: '操作失敗'
          message: "更動排序失敗"

      # 初始化資料
      context.dispatch 'list.init.data'

    # 匯入資料
    'list.import.data': (context, importFile) ->
      promise = listSettingObj.importData importFile
      promise.then () ->
        context.dispatch 'alert',
          style: 'success'
          title: '匯入成功'
          message: "匯入成功"
      .catch () ->
        context.dispatch 'alert',
          style: 'error'
          title: '匯入失敗'
          message: "匯入失敗"

    # 匯出資料
    'list.exportData': (context) ->
      promise = listSettingObj.exportData()
      promise.then () ->
        context.dispatch 'alert',
          style: 'success'
          title: '匯出成功'
          message: "匯出成功"
      .catch () ->
        context.dispatch 'alert',
          style: 'error'
          title: '匯出失敗'
          message: "匯出失敗"

    # 同步search checkbox狀態
    'list.syncSearchStatus': (context, data) ->
      context.commit 'list.syncSearchStatus', data

      # 取消search checkbox勾選時, 清除search條件
      if data.used == false
        listSettingObj._clearSearchCondition(data.key)
        context.commit 'list.update.filter'


    # 設定search資料
    'list.setSearch': (context, data) ->
      if data.info == 'all'
        listSettingObj._clearSearchCondition data.key
      else
        listSettingObj._setSearch data

      window.rootComponent.$nextTick () ->
        context.commit 'list.update.filter'

        # 初始化資料
        context.dispatch 'list.init.data'

    'list.search.clear': (context) ->
      context.commit 'list.update.filter'
      context.dispatch 'list.init.data'

    'list.init.uniqueName': (context, uniqueName) ->
      context.commit 'list.init.uniqueName', uniqueName

    'list.inject.filter': (context, filter) ->
      # 是空物件(沒設定filter), 就帶出預設sorting或是search
      if filter.search != undefined
        isEmpty = true
        for node of filter.search
          isEmpty = false
        listSettingObj.search = filter.search if !isEmpty
      if filter.sort != undefined
        isEmpty = true
        for node of filter.sort
          isEmpty = false
        listSettingObj.sort = filter.sort if !isEmpty
      listSettingObj.page = filter.page
      context.commit 'list.update.filter'
      context.dispatch 'list.init.data'

    'list.keep.history': (context, config) ->
      context.commit 'list.keep.history', config
  getters:
    'listData': (state) ->
      return state.listData

    'listConfig': (state) ->
      return state.listConfig

    'listSetting': (state) ->
      return state.listSetting

    'filterData':(state) ->
        return state.filterData

    'pager': (state) ->
      return state.pager

    'checkStatus': (state) ->
      return state.checkStatus

    'searchStatus': (state) ->
      return state.searchStatus
    quickSuccess: (state) ->
      return state.quickSuccess
    quickError: (state) ->
      return state.quickError
    uniqueName: (state) ->
      return state.uniqueName

    actionButton: (state) ->
      return state.actionButton