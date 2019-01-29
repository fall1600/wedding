<template>
  <span>
    {{filter(renderData)}}
  </span>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  props: ['data', 'config']
  methods:
    filter: (data) ->
      if @config.colum != undefined
        if @config.colum.filter == 'quantity'
          return @quantityFilter(data)
      return data
    quantityFilter: (data) ->
      if parseInt(data) > 0
        return "+#{data}"
      return data
  computed:
    viewData: () ->
      return @$store.getters.view.data
    renderData: () ->
      return @data if @config.key.split('.')[1] == undefined
      return @viewData[@config.array][@config.index][@config.key.split('.')[0]][@config.key.split('.')[1]]
</script>