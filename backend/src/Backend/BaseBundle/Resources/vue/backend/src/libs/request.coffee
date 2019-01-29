init = () ->
  pendingRequests = {}
  (($) ->
    defaultConfig =
      url: ''
      type:  'POST'
      dataType: 'JSON'
      contentType: 'text/plain'
      overrideMethod: false
      apiBase: null
      data: null
      global: true
      jsonDataRequest: true
      success: () ->
        return
      error: () ->
        return
    makeUniqueUrl = (config) ->
      "#{config.url}##{JSON.stringify(config.data)}"
    appendCallback = (config) ->
      isAppend = true
      if pendingRequests[makeUniqueUrl(config)] == undefined
        pendingRequests[makeUniqueUrl(config)] = []
        isAppend = false
      pendingRequests[makeUniqueUrl(config)].push
        success: config.success
        error: config.error
      isAppend
    cachedRequest = (config) ->
      return if appendCallback(config)
      configUrl = makeUniqueUrl(config)
      config.success = () ->
        for callback in pendingRequests[configUrl]
          callback.success.apply null, arguments
        delete pendingRequests[configUrl]
        return
      config.error = () ->
        for callback in pendingRequests[configUrl]
          callback.error.apply null, arguments
        delete pendingRequests[configUrl]
        return
      $.ajax config
      return
    $.requestConfig = (config) ->
      defaultConfig = $.extend defaultConfig, config
      return
    $.request  = (config) ->
      if defaultConfig.data != config.data
        defaultConfig.data = null
      requestConfig = $.extend defaultConfig, config
      if requestConfig.overrideMethod && requestConfig.type != 'GET' && requestConfig.type != 'POST'
        if /\?/.test requestConfig.url
          seprator = '&'
        else
          seprator = '?'
        requestConfig.url = "#{requestConfig.url}#{seprator}_method=#{encodeURI(requestConfig.type)}"
        requestConfig.type = 'POST'
      if requestConfig.jsonDataRequest && requestConfig.data && requestConfig.type != 'GET'
        requestConfig.data = JSON.stringify requestConfig.data
      requestConfig.url = "#{requestConfig.apiBase}#{requestConfig.url}"
      if requestConfig.type == 'GET'
        cachedRequest requestConfig
      else
        $.ajax requestConfig
      return
    return
  ) jQuery

export default init()
