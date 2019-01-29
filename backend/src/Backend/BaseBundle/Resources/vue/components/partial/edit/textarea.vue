<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <textarea :id="eventName + '_' + toolConfig.name" class="form-control" :placeholder="usePlaceholder()"
                  v-on:keyup="syncData" v-on:change="syncData" :readonly="useReadonly()">{{ text }}</textarea>

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
        @$root.$on 'form.showError', @handleError
    props: ['toolConfig']
    data: () ->
        errorMsg: ''
        eventName: @$route.name
    computed:
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
            return source
        text: () ->
            if @data != undefined && @data != null
                return @data.trim()
            else
                @$store.dispatch 'edit.syncData',
                    key: @toolConfig.name
                    deep: @toolConfig.deep
                    repeated: @toolConfig.repeated
                    parent: @toolConfig.parent
                    dataIndex: @toolConfig.dataIndex
                    value: ''
                return ''
    methods:
        handleError: (response) ->
            for key of response
                if key == @toolConfig.name
                    @errorMsg = response[key]
                    return
            @errorMsg = ''
        usePlaceholder: () ->
            if @toolConfig.config != undefined
                if @toolConfig.config.placeholder != undefined
                    return @toolConfig.config.placeholder
                else
                    return ''
            else
                return ''
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
        useReadonly: () ->
            if @toolConfig.readonly != undefined
                return @toolConfig.readonly
            else
                return false
        syncData: (event) ->
            me = @
            # 同步資料
            @$store.dispatch 'edit.syncData',
                key: me.toolConfig.name
                deep: me.toolConfig.deep
                repeated: @toolConfig.repeated
                parent: @toolConfig.parent
                dataIndex: @toolConfig.dataIndex
                value: event.target.value
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    textarea
        min-height: 150px
        width: 100%
</style>