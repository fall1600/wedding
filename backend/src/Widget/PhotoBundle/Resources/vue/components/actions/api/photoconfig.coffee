module.exports = (api) ->
  api.photoconfig =
    search: (data) ->
      api.request "GET", "/photoconfigs", data
    create: (data) ->
      api.request "POST", "/photoconfigs", data
    read: (id) ->
      api.request "GET", "/photoconfig/#{id}"
    update: (id, data) ->
      api.request  "PUT", "/photoconfig/#{id}", data
    delete: (id) ->
      api.request  "DELETE", "/photoconfig/#{id}"
    batch: (ids, action, column) ->
      api.request  "PUT", "/photoconfigs",
        ids: ids
        action: action
        column: column
    getChoice: () ->
      new Promise (resolve, reject) ->
        choices = [
          { "id": "inbox", "name": "form.choice.photo_config_item.inbox" }
          { "id": "outbox", "name": "form.choice.photo_config_item.outbox" }
          { "id": "crop", "name": "form.choice.photo_config_item.crop" }
          { "id": "resizecrop", "name": "form.choice.photo_config_item.resizecrop" }
          { "id": "fit", "name": "form.choice.photo_config_item.fit" }
        ]
        resolve choices