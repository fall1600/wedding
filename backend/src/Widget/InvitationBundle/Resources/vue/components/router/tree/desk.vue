<template>
  <div class="page-content">
    <category :useCustomize="customize"></category>
  </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
  module.exports =
    mixins: [require "components/backendbase/mixins/categoryConfig.coffee"]
#    data: () ->
#      customize: [
#        require "../../customize/channel_tree/config.vue"
#      ]
    methods:
      getModel: () ->
        require('components/widgetinvitation/actions/tree/desk.coffee')
</script>