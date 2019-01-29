class Session
  _registerEvent: () ->
    me = @
    sessionStorageTransfer = (event) ->
      return if !event.newValue
      if event.key == 'getSessionStorage'
        me.emitUpdateSession()
        return
      if event.key == 'sessionStorage' && !me.sessionUpdateLock
        me.sessionUpdateLock = true
        data = JSON.parse(event.newValue)
        window.sessionStorage.clear()
        for key, value of data
          window.sessionStorage.setItem key, value
        window.dispatchEvent(new Event('sessionUpdate'))
        me.sessionUpdateLock = false
      return
    window.addEventListener "storage", sessionStorageTransfer, false
    return
  constructor: () ->
    @sessionUpdateLock = false
    @_registerEvent()
    @emitGetSession()
    return
  set: (key, value) ->
    window.sessionStorage.setItem key, JSON.stringify(value)
    @emitUpdateSession()
    return
  get: (key) ->
    try
      value = window.sessionStorage.getItem key
      if typeof value != 'string'
        return null
      return JSON.parse(value)
    catch e
      return null
  delete: (key) ->
    window.sessionStorage.removeItem key
  emitUpdateSession: () ->
    return if @sessionUpdateLock
    @sessionUpdateLock = true
    window.localStorage.setItem('sessionStorage', JSON.stringify(window.sessionStorage))
    window.localStorage.removeItem('sessionStorage')
    @sessionUpdateLock = false
    return
  emitGetSession: () ->
    if !window.sessionStorage.length
      window.localStorage.setItem 'getSessionStorage', 'foobar'
      window.localStorage.removeItem 'getSessionStorage', 'foobar'
    return

if window.session == undefined
  window.session = new Session()

export default window.session
