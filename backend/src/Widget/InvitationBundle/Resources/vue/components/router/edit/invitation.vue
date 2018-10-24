<template>
  <div class="page-content">
    <!-- useCustomize: 載入客製元件 -->
    <edit :editSetting="editSetting" :useCustomize="customize" :subTitle="subTitle" :grid="grid"></edit>
  </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
  module.exports =
    mixins: [require "components/backendbase/mixins/editConfig.coffee"]
    data: () ->
      customize: []
    events:
      "form.postSaveSuccess": (result, redirect) ->
        if redirect != undefined
          @$router.replace
            name: "about-list"
        else
          @$router.replace
            name: "about-edit"
            params:
              id: result.id
        return
    methods:
      getModel: () ->
        require('components/widgetinvitation/actions/edit/invitation.coffee')
</script>
