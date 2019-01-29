<template>
    <div>
        <single-layout v-if="$route.name == 'login'"></single-layout>
        <frame-layout v-if="$route.name != 'login'"></frame-layout>
        <partial-alert></partial-alert>
        <partial-modal></partial-modal>
    </div>
</template>

<style src="bootstrap/dist/css/bootstrap.css"></style>
<style src="font-awesome/css/font-awesome.css"></style>
<style src="assets/backendbase/css/custom.css"></style>
<style src="animate.css/animate.css"></style>

<script lang="babel!coffee" type="text/coffeescript">
import "bootstrap/dist/js/bootstrap.js"
import 'assets/backendbase/js/custom.js'
import 'gentelella/vendors/iCheck/icheck.js'
export default _ =
  methods:
    setClass: () ->
      if @$route.name == 'login'
        $('body').attr 'class', 'login'
      else
        $('body').attr 'class', 'nav-md'
      return
  watch:
    '$route':  (ro, from) ->
      @setClass()
      return
  components:
    "single-layout":  require "components/backendbase/layout/single-layout.vue"
    "frame-layout":  require "components/backendbase/layout/frame-layout.vue"
    "partial-alert": require "components/backendbase/partial/alert.vue"
    "partial-modal": require "components/backendbase/partial/modal.vue"
  mounted: () ->
    @setClass()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .fade
        &-enter-active, &-leave-active
            transition: opacity .5s
        &-enter, &-leave-active
            opacity: 0
    label
      margin-bottom: 0
</style>
