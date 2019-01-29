module.exports = {
  computed:
    token: () ->
      @$store.getters.token
  methods:
    hasRole: (requiredRoles) ->
      return false if !@token || !@token.getInfo()
      return true if @token.getInfo().data.roles.indexOf('ROLE_SUPERADMIN') >= 0
      for role in @token.getInfo().data.roles
        return true if requiredRoles.indexOf(role) >= 0
      false
    findInConfigWithRole: (name, configs) ->
      for config in configs
        if typeof config == 'object'
          if config.name == name && @hasRole(config.roles)
            return true
        if config == name
          return true
      false
}