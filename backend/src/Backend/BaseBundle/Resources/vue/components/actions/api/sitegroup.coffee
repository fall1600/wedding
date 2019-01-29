roles = require('./roles.coffee')

module.exports = (api) ->
  api.sitegroup =
    search: (data) ->
      api.request "GET", "/sitegroups", data
    create: (data) ->
      api.request "POST", "/sitegroups", roles.storeToApi(data)
    read: (id) ->
      new Promise (resolve, reject) ->
        api.request  "GET", "/sitegroup/#{id}"
        .then (result) ->
          resolve roles.apiToStore(result)
        .catch (error) ->
          reject error
    update: (id, data) ->
      api.request  "PUT", "/sitegroup/#{id}", roles.storeToApi(data)
    delete: (id) ->
      api.request  "DELETE", "/sitegroup/#{id}"
    batch: (ids, action, column) ->
      api.request  "PUT", "/sitegroups",
        ids: ids
        action: action
        column: column
    getSiteUsers: () ->
      api.request  "GET", "/siteusers/all"
    setSiteUsers: (id, data) ->
      api.request  "POST", "/sitegroup/#{id}/siteuser", data      
    allRoles: () ->
      new Promise (resolve, reject) ->
        api.request  "GET", "/allroles"
        .then (result) ->
          resolve roles.apiTypeRolesToStore(result)
        .catch (error) ->
          reject error
