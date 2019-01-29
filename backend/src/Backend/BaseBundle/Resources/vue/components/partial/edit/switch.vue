<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))">
            {{ toolConfig.text|trans }}
        </label>
        <div class="switch-wrap" :class="toolGrid(toolConfig.toolGrid)">
            <label class="switch-container">
                <input type="checkbox" class="switch-status" v-bind:true-value="1" v-bind:false-value="0" v-model="realCheckStatus" v-on:change="updateStatusText"/>
                <span class="switchery switchery-default" v-if="!useReadonly()">
                    <small></small>
                </span>
                {{ statusText|trans }}
            </label>

            <p class="help-block">{{ useHelp()|trans }}</p>
            <div class="errorMsg text-danger" v-if="errorMsg != ''">
                <h5>
                    <i class="fa fa-warning"></i>
                    {{ errorMsg|trans }}
                </h5>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    mounted: () ->
        @initData()
        @updateStatusText()
    props: ['toolConfig']
    data: () ->
        realCheckStatus: 0
        statusText: false
        errorMsg: ''
    computed:
        editSetting: () ->
            return @$store.getters.editSetting
        data: () ->
            dataKey = @toolConfig.name
            if @toolConfig.deep == true
                node = dataKey.split '.'
                if @$store.getters.editSetting.dataRow[node[0]] != undefined
                    source = @$store.getters.editSetting.dataRow[node[0]][node[1]]
                if @$store.getters.editSetting.dataRow[node[0]] == undefined
                    source = undefined
            else if @toolConfig.repeated == true
                if @toolConfig.parent != undefined
                    source = @$store.getters.editSetting.dataRow[@toolConfig.parent][@toolConfig.dataIndex][dataKey]
                else
                    source == undefined
            else
                source = @$store.getters.editSetting.dataRow[dataKey]

            if source == undefined || source == null
                @realCheckStatus = 0
                return 0
            else if source == false || source == 0
                @realCheckStatus = 0
                return 0
            else if source == true || source == 1
                @realCheckStatus = 1
                return 1
    methods:
        initData: () ->
            if @data == undefined || @data == null
                @realCheckStatus = 0
            else
                @realCheckStatus = @data
            @syncData()
        handleError: (response) ->
            for key of response
                if key == @toolConfig.name
                    @errorMsg = response[key]
                    return
            @errorMsg = ''
        useRequired: () ->
            if @toolConfig.required == true
                return 'required'
            else
                return ''
        useHelp: () ->
            if @toolConfig.config != undefined
                if @toolConfig.config.help != undefined
                    return @toolConfig.config.help
                else
                    return ''
            else
                return ''
        updateStatusText: () ->
            me = @
            if(@realCheckStatus == 1)
                @statusText = @toolConfig.config.on
            else
                @statusText = @toolConfig.config.off

            @syncData()
        syncData: () ->
            @$store.dispatch 'edit.syncData',
                key: @toolConfig.name
                deep: @toolConfig.deep
                repeated: @toolConfig.repeated
                parent: @toolConfig.parent
                dataIndex: @toolConfig.dataIndex
                value: @realCheckStatus
        useReadonly: () ->
            if @toolConfig.readonly != undefined
                return @toolConfig.readonly
            else
                return false
    watch:
        data:
            deep: true
            handler: () ->
                @realCheckStatus = @data
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .switch-wrap
        margin-top: 7px

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