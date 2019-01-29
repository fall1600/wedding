module.exports = (api) ->
  api.siteConfig =
    read: () ->
      api.request "GET", "/setup/mail"
    update: (data) ->
      api.request  "PUT", "/setup/mail", data
