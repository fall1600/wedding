class abstractList
  constructor: (@api) ->
    @search = {}  # [{id: {value: "...."}, {name: {value: "zxxx"}}]
    @sort = {}    # {id: "asc", name: "desc"}
    @page = 1

    @pager = {}  #{page: 1, pages: 3, rows: 21}
    @listData = [] # [{id: 1212, name: "ashjakjshka"}, {id: 1212, name: "ashjakjshka"}]

    @listConfig =
      labels: {}   # {id: "#", name: "list.label.name"} ...
      type:   {}   # {id: "integer", name: "string", gender: "choice"}
      sort:   []   # ["id", "name"]
      quickEdit:  []  # ["id", "name"]
      search: []   # ["id", "name"]
      config: {}
    @_convertConfig @_getConfig()
    @_init()
    return
  _convertConfig: (configData) ->
    @listConfig =
      labels: {}
      type: {}
      sort: []
      quickEdit: []
      search: []
      searchConfig: {}
      batch: []
      customBatch: []
      batchSetting: {}
      extra: configData.extra
      action: []
      config: {}
      short: {}
      defaultSort: {}
    for column, value of configData.list
      @listConfig.labels[column] = value.label
      @listConfig.type[column] = value.type
      if value.sort != undefined && value.sort
        @listConfig.sort.push column
      if value.quick != undefined && value.quick
        @listConfig.quickEdit.push column
      if value.search != undefined && value.search
        @listConfig.search.push column
      if value.batch != undefined && value.batch
        @listConfig.batch.push column
      if value.config != undefined && value.config
        @listConfig.config[column] = value.config
      if value.searchConfig != undefined && value.searchConfig
        @listConfig.searchConfig[column] = value.searchConfig
      if value.defaultSorting != undefined && value.defaultSorting
        @sort[column] = value.defaultSorting
        @listConfig.defaultSort[column] = value.defaultSorting
      if value.defaultFilter != undefined && value.defaultFilter
        @search[column] = value.defaultFilter
      if value.short != undefined && value.short
        @listConfig.short[column] = value.short
      if value.batchSetting != undefined && value.batchSetting
        @listConfig.batchSetting[column] = value.batchSetting
    for value in configData.action
      @listConfig.action.push value
    if configData.customBatch != undefined
      for batch in configData.customBatch
        @listConfig.customBatch.push batch
    return
  getListConfig: () -> @listConfig
  getListData: () -> @listData
  getPager: () -> @pager
  getFilterData: () ->
    {
      search: @search
      sort:   @sort
      page:   @page
    }

  # 切換分頁
  setPage: (@page) ->
    return

  _setSearch: (data)->
    @search[data.key] = data.info
    return

  _clearSearchCondition: (key) ->
    delete @search[key]

  # 設定排序
  setSort: (sortKey) ->
    if @sort[sortKey] == undefined
      @sort[sortKey] = 'asc'
    else if @sort[sortKey] == 'asc'
      @sort[sortKey] = 'desc'
    else if @sort[sortKey] == 'desc'
      delete @sort[sortKey]
  clearSort: (key) ->
    delete @sort[key]
# 批次管理
  batch: (ids, action, colum) ->
    return new Promise()

  # 快速編輯
  quickEdit: (data) ->
    return

  # 切換狀態
  changeDataStatus: (id, value) ->
    return

  # 刪除資料
  deleteData: (id) ->
    return

  # 移動排序
  changeSeq: (id, move) ->
    return

  # 匯入資料
  importData: (importFile) ->
    return

  # 匯出資料
  exportData: () ->
    return

  requestData: () ->
    me = @
    new Promise (resolve, reject) ->
      me.request()
      .then (result) ->
        me._setRequestData result
        resolve result
        return
      .catch (error) ->
        reject error
        return
  _searchData: () ->
    {
      search: @search
      sort: @sort
      page: @page
    }
  _setRequestData: (result) ->
    @listData = result.data
    if result.pager
      @pager = result.pager
      @page = result.pager.page
    return
  _init: () ->
    return
module.exports = abstractList
