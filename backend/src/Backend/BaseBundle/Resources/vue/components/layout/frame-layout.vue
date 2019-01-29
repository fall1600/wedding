<template>
    <div class="container body">
        <div class="main_container">
            <transition name="fade">
                <partial-loading v-if="loading"></partial-loading>
            </transition>
            <div class="col-md-3 left_col hidden-print">
                <partial-sidebar></partial-sidebar>
            </div>
            <partial-top-nav></partial-top-nav>
            <div class="right_col" role="main">
                <partial-header></partial-header>
                <router-view class="transition" transition="transition" transition-mode="out-in"></router-view>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
# <script lang="coffee" type="text/coffeescript">
module.exports =
  mounted: () ->
    require("assets/backendbase/js/custom.js")()
  components:
    'partial-loading': require "components/backendbase/partial/loading.vue"
    'partial-sidebar': require "components/backendbase/partial/sidebar.vue"
    'partial-top-nav': require "components/backendbase/partial/top-nav.vue"
    'partial-header': require "components/backendbase/partial/title.vue"
  computed:
    tokenExp: () ->
        return @$store.getters.token.info.exp
    loading: ->
        return @$store.getters.loading
  watch:
    $route: () ->
      now = Math.floor(new Date().getTime()/1000)
      if now >= @tokenExp
          @$router.push
            name: 'logout'
</script>

<style lang="sass?indentedSyntax" type="text/sass">
  @import "../assets/sass/base.sass"
  .fade
    &-enter-active, &-leave-active
      transition: opacity .2s
    &-enter, &-leave-active
      opacity: 0
  .left_col
    +transition(.2s)
</style>