class abstractCategory
  constructor: (@api) ->
    config = {}
    data = []
    @_init()
  _init: () ->
    return
  getConfig: () ->
    return
  getRoot: () ->
    return
  getChildren: () ->
    return
  create: () ->
    return
  update: () ->
    return
  delete: () ->
    return
  moveInside: () ->
    return
  moveAfter: () ->
    return
  save: () ->
    return
module.exports = abstractCategory