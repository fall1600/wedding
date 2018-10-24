#mock = require 'components/backendbase/mock/mock.coffee'
module.exports = (api) ->
  api.invitation =
    read: (id) ->
      return api.request "GET", "/invitation/#{id}"
    search: (data) ->
      return api.request "GET", "/invitations", data
    create: (data) ->
      return api.request "POST", "/invitations", data
    update: (id, data) ->
      return api.request "PUT", "/invitation/#{id}", data
    delete: (id) ->
      return api.request "DELETE", "/invitation/#{id}"
