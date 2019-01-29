<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <input :id="eventName + '_' + toolConfig.name" type="password" class="form-control" :placeholder="usePlaceholder()"
                   v-model="inputText" v-on:keyup="syncData"/>

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
            @$root.$on 'form.showError', @handleError
        data: () ->
            inputText: @data
            errorMsg: ''
            eventName: @$route.name
        props: ['toolConfig']
        computed:
            data: () ->
                dataKey = @toolConfig.name
                if @toolConfig.deep == true
                    node = dataKey.split '.'
                    if @$store.getters.editSetting.dataRow[node[0]] != undefined
                        source = @$store.getters.editSetting.dataRow[node[0]][node[1]]
                    if @$store.getters.editSetting.dataRow[node[0]] == undefined
                        source = undefined
                else
                    source = @$store.getters.editSetting.dataRow[dataKey]
                return source
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
            initData: () ->
                if @data == undefined
                    @inputText = ''
                    @syncData()
                else
                    @inputText = @data
                    @$store.dispatch 'edit.syncData',
                        key: @toolConfig.name
                        deep: @toolConfig.deep
                        value: @data
            syncData: () ->
                @$store.dispatch 'edit.syncData',
                    key: @toolConfig.name
                    deep: @toolConfig.deep
                    value: @inputText
        watch:
            data:
                deep: true
                handler: () ->
#                    @initData()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>

</style>