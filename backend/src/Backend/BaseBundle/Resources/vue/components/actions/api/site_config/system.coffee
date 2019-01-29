module.exports = (api) ->
  api.siteConfig =
    read: () ->
      api.request "GET", "/setup/system"
    update: (data) ->
      api.request  "PUT", "/setup/system", data
