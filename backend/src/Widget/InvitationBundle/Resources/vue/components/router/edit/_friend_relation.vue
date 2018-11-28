<template>
  <div class="form-group col-xs-12">
    <label class="control-label col-md-4 col-sm-4 col-xs-12 required">
      {{'form.label.product.category'| trans}}
    </label>

    <div class="col-md-8 col-sm-8 col-xs-12">
      <select class="form-control" v-model="categories">
        <option v-for="option in options" :value="option.id">
          {{option.name}}
        </option>
      </select>
    </div>
  </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
  categoryApi = require '../../actions/api/category.coffee'
  module.exports =
    data: () ->
      _api: null
      options: null
      categories: null
    created: () ->
      @_api = @$store.getters.api
      categoryApi(@_api)
      @loadCategories()
    methods:
      loadCategories: () ->
        me = @
        @options = null
        @_api.category.getRoot('friend')
        .then (root) ->
          me._api.category.getChildren(root.id)
        .then (result) ->
          me.options = result
          me.init()
      init: () ->
        return if !@formData
        if @formData.categories
          @categories = @formData.categories
          return
        @categories = @options[0].id
      sync: () ->
        @$store.dispatch 'edit.syncData',
          key: 'categories'
          value: @categories
    computed:
      api: () -> @$store.getters.api
      formData: () -> @$store.getters.editSetting.dataRow
    watch:
      categories: () ->
        @sync()
</script>

<style lang="sass" type="text/sass" scoped></style>