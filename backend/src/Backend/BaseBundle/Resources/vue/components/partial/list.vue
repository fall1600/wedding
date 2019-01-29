<template>
    <div class="page-content">
        <!--列表動作-->
        <pagination :pager="pager"></pagination>
        <list-search></list-search>
        <list-manage :position="'top'" :batch-config="batchConfig"></list-manage>
        <!--列表-->
        <list-table v-if="!loading" class="table-responsive" :batch-config="batchConfig"></list-table>
        <!--列表動作-->
        <div class="control-tools">
            <list-manage :position="'bottom'" :batch-config="batchConfig"></list-manage>
            <pagination :pager="pager"></pagination>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
# <script lang="coffee" type="text/coffeescript">
module.exports =
  mixins:  [
    require "components/backendbase/mixins/lists.coffee"
    require "components/backendbase/mixins/token.coffee"
  ]
  computed:
    loading: () ->
      return @$store.getters.loading
    filterData: () ->
      return @$store.getters.filterData
    dataReady: () ->
      return @$store.getters.dataReady
    pager: () ->
      return @$store.getters.pager
    listConfig: () ->
      return @$store.getters.listConfig
    checkStatus: () ->
      return @$store.getters.checkStatus
    searchStatus: () ->
      return @$store.getters.searchStatus
    loading: () ->
      return @$store.getters.loading
    batchSetting: () ->
      @listConfig.batchSetting
    batchConfig: () ->
      action = []
      for colum in @listConfig.batch
        for key of @batchSetting
          for setting in @batchSetting[key]
            action.push
              label: setting.label
              name: setting.label
              colum: colum
              action: 'value'
              value: setting.value
      if @findInConfigWithRole 'delete', @listConfig.action
        action.push
          name:  "action.delete"
          column: "delete"
          action: "delete"
      if @listConfig.batch != undefined
        for customBatch in @listConfig.customBatch
          if customBatch.roles == undefined || @hasRole(customBatch.roles)
            action.push customBatch
      return action
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .control-tools
        @extend %clearfix
    .control-tools
        position: relative
    // .pagination-container
    //     position: absolute
    //     right: 0
    //     bottom: 0
</style>