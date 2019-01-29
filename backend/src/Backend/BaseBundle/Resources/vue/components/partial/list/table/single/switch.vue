<template>
    <label class="switch-container">
        <input type="checkbox" class="switch-status"
           v-model="realCheckStatus" v-on:change="changeStatus"
           v-bind:true-value="trueValue" v-bind:false-value="falseValue">
        <span class="switchery switchery-default">
            <small></small>
        </span>
    </label>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        props: ['column', 'position'],
        data: () ->
            realCheckStatus: @column
        computed:
            listData: () -> @$store.getters.listData
            listConfig: () -> @$store.getters.listConfig
            config: () -> @listConfig.config[@position.key]
            trueValue: () ->
                return true if @config == null || @config == undefined
                return true if @config.value == null || @config.value == undefined
                return true if @config.value.true == null || @config.value.true == undefined
                return @config.value.true
            falseValue: () ->
                return false if @config == null || @config == undefined
                return false if @config.value == null || @config.value == undefined
                return false if @config.value.false == null || @config.value.false == undefined
                return @config.value.false
        methods:
            changeStatus: () ->
                @$store.dispatch 'list.syncData',
                    row: @position.row
                    key: @position.key
                    value: @realCheckStatus

                id = @listData[@position.row].id
                key = @position.key
                data =
                    "#{key}": @realCheckStatus
                @$store.dispatch 'list.quickEdit',
                    id: id
                    data: data
        watch:
            # 父元件更排序時要更新
            checkStatus: () ->
                @realCheckStatus = @checkStatus
</script>

<style src="gentelella/vendors/switchery/dist/switchery.css"></style>
<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .switch-container
        .switch-status
            display: none

        .switch-status:checked~.switchery
            background-color: rgb(38, 185, 154)
            border-color: rgb(38, 185, 154)
            box-shadow: rgb(38, 185, 154) 0px 0px 0px 11px inset
            transition: border 0.4s, box-shadow 0.4s, background-color 1.2s

            small
                left: 12px
                transition: background-color 0.4s, left 0.2s
                background-color: rgb(255, 255, 255)
</style>