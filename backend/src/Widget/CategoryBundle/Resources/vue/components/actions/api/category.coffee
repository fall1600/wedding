module.exports = (api) ->
  api.category =
    getRoot:(thread) ->
      api.request "GET", "/category/#{thread}"
    getChildren: (id) ->
      api.request "GET", "/category/#{id}"
    create: (data) ->
      api.request "POST", "/category/#{data.target}", data
    update: (data) ->
      api.request "PUT", "/category/#{data.id}", data
    delete: (id) ->
      api.request "DELETE", "/category/#{id}"
    moveInside: (move, target) ->
      params = {
        target_id: target
        position: 'inside'
      }
      api.request "PUT", "/category/#{move}/moveto", params
    moveAfter: (move, target) ->
      params = {
        target_id: target
        position: 'after'
      }
      api.request "PUT", "/category/#{move}/moveto", params