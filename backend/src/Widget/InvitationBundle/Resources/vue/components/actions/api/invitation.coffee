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
            value: "no"
          }
          {
            key: "出席台北場"
            value: "taipei"
          }
          {
            key: "出席嘉義場"
            value: "chiayi"
          }
          {
            key: "出席兩場"
            value: "both"
          }
          {
            key: "禮到人未到"
            value: "blessing"
          }
          {
            key: "尚未確定"
            value: "notsure"
          }
        ]
        resolve result
    getAllKnownFrom: () ->
      return new Promise (resolve, reject) ->
        result = [
          {
            key: "男方"
            value: "male"
          }
          {
            key: "女方"
            value: "female"
          }
        ]
        resolve result

