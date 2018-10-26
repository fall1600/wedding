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
    getAllAttends: () ->
      return new Promise (resolve, reject) ->
        result = [
          {
            key: "不出席"
            value: "不出席"
          }
          {
            key: "出席"
            value: "出席"
          }
          {
            key: "禮到人未到"
            value: "禮到人未到"
          }
          {
            key: "尚未確定"
            value: "尚未確定"
          }
        ]
        resolve result
    getAllKnownFrom: () ->
      return new Promise (resolve, reject) ->
        result = [
          {
            key: "男方"
            value: "男方"
          }
          {
            key: "女方"
            value: "女方"
          }
        ]
        resolve result
