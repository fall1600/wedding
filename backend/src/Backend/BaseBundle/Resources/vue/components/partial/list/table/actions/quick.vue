<template>
    <!--快速編輯-->
    <div class="quick-container">
        <span class="btn" :class="quickEditButtonStyle" v-on:click="quickEditListItem(dataId)">
            <div class="text" v-if="quickEditStatus == false">
                {{'action.quick'|trans}}
            </div>

            <div class="text" v-if="quickEditStatus == true">
                {{'action.save'|trans}}
            </div>
        </span>

        <span class="btn btn-default" v-if="quickEditStatus == true" @click="switchEditStatus()">
            <div class="text">
                {{'action.cancel_quick'|trans}}
            </div>
        </span>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['dataId']
    mounted: () ->
        @$root.$on 'list.trigger.quickedit', @quickEditListItem
    beforeDestroy: () ->
        @$root.$off 'list.trigger.quickedit'
    data: () ->
        quickEditStatus: false
        quickEditButtonStyle: 'btn-success'
    computed:
        listConfig: () ->
            return @$store.getters.listConfig
        listData: () ->
            return @$store.getters.listData
    methods:
        switchEditStatus: () ->
            for singleData, index in @listData
                if singleData.id == @dataId
                    rowIndex = index
            me = @
            @$root.$emit 'list.quickedit.cancel', rowIndex
#            @$store.dispatch 'list.init.data'
            @quickEditStatus = !@quickEditStatus
            if @quickEditStatus == true
                me.quickEditButtonStyle = 'btn-warning'
            else
                me.quickEditButtonStyle = 'btn-success'
        # 快速編輯事件
        quickEditListItem: (dataId) ->
            return if dataId != @dataId
            for singleData, index in @listData
                if singleData.id == dataId
                    rowIndex = index
            me = @
            @$root.$emit 'list.quickedit', rowIndex
            @quickEditStatus = !@quickEditStatus
            if @quickEditStatus == true
                me.quickEditButtonStyle = 'btn-warning'
            else
                me.quickEditButtonStyle = 'btn-success'
                data = {}

                # 只取允許編輯的欄位封裝成一個data object
                for key in @listConfig.quickEdit
                    data["#{key}"] = @listData[rowIndex][key]
                me.$store.dispatch 'list.quickEdit',
                    id: @listData[rowIndex].id
                    data: data
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
.quick-container
  display: inline-block
</style>