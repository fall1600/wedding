abstractCategory = require "components/backendbase/actions/abstract-category.coffee"

class Tree extends abstractCategory
  _init: () ->
    require('components/widgetcategory/actions/api/category.coffee') @api
  getConfig: () ->
    require 'components/widgetinvitation/config/tree/desk.coffee'
  getRoot: () ->
    @api.category.getRoot 'desk'
  getChildren: (id) ->
    @api.category.getChildren id
  create: (data) ->
    @api.category.create data
  update: (data) ->
    @api.category.update data
  delete: (id) ->
    @api.category.delete id
  moveInside: (move, target) ->
    @api.category.moveInside(move, target)
  moveAfter: (move, target) ->
    @api.category.moveAfter(move, target)

module.exports = (api) ->
  new Tree(api)