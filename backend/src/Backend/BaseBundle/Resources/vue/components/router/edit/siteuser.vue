<template>
    <div class="page-content">
        <!-- useCustomize: 載入客製元件 -->
        <edit :editSetting="editSetting" :useCustomize="customize"></edit>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mixins: [require "components/backendbase/mixins/editConfig.coffee"]
    data: () ->
      customize: []
#      redirect:
#        name: 'product-list'
#        query:
#          page: 1
#          sort: 'updated_at=desc'
    events:
      "form.postSaveSuccess": (result, redirect) ->
        if redirect != undefined
          @$router.replace
            name: "siteuser-list"
        else
          @$router.replace
            name: "siteuser-edit"
            params:
              id: result.id
        return
    methods:
      getModel: () ->
        require('components/backendbase/actions/edit/siteuser.coffee')
</script>
