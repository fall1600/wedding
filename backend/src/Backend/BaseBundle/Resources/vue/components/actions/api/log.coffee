module.exports = (api) ->
  api.log =
    search: (data) ->
      api.request "GET", "/logs", data
    read: (id) ->
      api.request "GET", "/log/#{id}"
    delete: (id) ->
      api.request  "DELETE", "/log/#{id}"
    batch: (ids, action, column) ->
      api.request  "PUT", "/logs",
        ids: ids
        action: action
        column: column