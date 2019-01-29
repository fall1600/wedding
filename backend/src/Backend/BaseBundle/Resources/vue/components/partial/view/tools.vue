<template>
  <div class="view-back-button hidden-print" :class="{ fixed: isFixed }">
    <!--<router-link :to="{ name: backToList($route.name) , query: filterConfig }" >-->
    <button class="btn btn-info" @click="back()">
      <i class="fa fa-arrow-left"></i>
      {{'form.button.previous'|trans}}
    </button>
    <!--</router-link>-->
  </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  props: ['backRoute']
  mounted: () ->
    @setFixed()
  data: () ->
    isFixed: false
  methods:
    backToList: (routeName) ->
      if @backRoute != undefined
        if @backRoute.name != undefined
          return @backRoute.name
      return routeName.replace('view', 'list')
    setFixed: () ->
      me = @
      $(window).scroll () ->
        position = $('body').scrollTop()
        if position > 130
          me.isFixed = true
        else
          me.isFixed = false
    back: () ->
      @$router.go -1
  computed:
    history: () -> @$store.getters.history
    filterConfig: () ->
      route = @backToList(@$route.name)
      history = JSON.parse sessionStorage.getItem('filter')
      filter =
        page: 1
        sort: 'updated_at=desc'
      if @backRoute != undefined
        if @backRoute.query != undefined
          filter = @backRoute.query
      filter = @history.getFilterQuery(route) if @history.getFilterQuery(route) != null
      return filter
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
  .view-back-button
    &.fixed
      position: fixed
      top: 0
      z-index: 10
</style>