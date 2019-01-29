import session from './sessionStorageShare.coffee'

class Token
  _readFromSession: () ->
    @session.get 'token'

  _updateToSession: () ->
    if @token == null
      @session.delete 'token'
    else
      @session.set 'token', @token
    return

  constructor: (@session) ->
    me = @
    @setToken @_readFromSession(), false
    window.addEventListener 'sessionUpdate', (event) ->
      me.setToken me._readFromSession()
      return
    , true

  getInfo: () ->
    @info

  isValidToken: () ->
    return false if @token == null
    now = Math.floor(new Date().getTime()/1000)
    return @getInfo().exp && @getInfo().exp > now
  renewToken: () ->
    return
  getToken: () ->
    @token
  clearToken: () ->
    @setToken null
  setToken: (token) ->
    @token = token
    if token != null
      @info = JSON.parse atob token.split('.')[1]
      now = Math.floor(new Date().getTime()/1000)
      surplusTime = @getInfo().exp - now
      me = @
      if surplusTime > (60 * 5)
        window.setTimeout () ->
          if me.renewToken != undefined
            me.renewToken()
        , (surplusTime - (60 * 5)) * 1000
      else if surplusTime <= (60 * 5) && surplusTime > 0
        @renewToken()
      else
        @setToken null
    @_updateToSession()
    return
  hasRole: (requiredRoles) ->
    return false if !@getInfo()
    roles = @getInfo().data.roles
    return true if roles.indexOf('ROLE_SUPERADMIN') >= 0
    for role in roles
      return true if requiredRoles.indexOf(role) >= 0
    false
token = new Token(session)

export default token
