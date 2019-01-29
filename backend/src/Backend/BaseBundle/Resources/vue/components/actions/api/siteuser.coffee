roles = require('./roles.coffee')

module.exports = (api) ->
  api.siteuser =
    search: (data) ->
      api.request "GET", "/siteuser/", data
    create: (data) ->
      api.request "POST", "/siteuser/", roles.storeToApi(data)
    read: (id) ->
      new Promise (resolve, reject) ->
        api.request  "GET", "/siteuser/#{id}"
        .then (result) ->
          resolve roles.apiToStore(result)
        .catch (error) ->
          reject error
    update: (id, data) ->
      api.request  "PUT", "/siteuser/#{id}", roles.storeToApi(data)
    delete: (id) ->
      api.request  "DELETE", "/siteuser/#{id}"
    batch: (ids, action, column, value) ->
      api.request  "PUT", "/siteusers",
        ids: ids
        action: action
        column: column
        value: value
    getSiteGroups: () ->
      api.request  "GET", "/sitegroups/all"
    allRoles: () ->
      new Promise (resolve, reject) ->
        api.request  "GET", "/allroles"
        .then (result) ->
          resolve roles.apiTypeRolesToStore(result)
        .catch (error) ->
          reject error
    setSiteGroups: (id, data) ->
      api.request "POST", "/siteuser/#{id}/sitegroup", data