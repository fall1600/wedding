class Roles
  storeToApi: (data) ->
    data = jQuery.extend(true, {}, data)
    if data.default_roles != undefined
      roles = []
      for role in data.default_roles
        roles.push role.id
      data.default_roles = roles
    return data
  apiTypeRolesToStore: (data) ->
    roles = []
    for group, groupRolesOrigin of data
      groupRoles = []
      for role of groupRolesOrigin
        groupRoles.push
          id: role
          name: role
      roles.push
        name: group
        child: groupRoles
    return roles
  apiToStore: (data) ->
    return data if data.default_roles == undefined
    roles = []
    for role in data.default_roles
      roles.push
        id: role
        name: role
    data.default_roles = roles
    data
module.exports = new Roles()