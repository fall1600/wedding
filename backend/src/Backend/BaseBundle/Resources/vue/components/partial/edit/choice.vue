<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <select :id="eventName + '_' + toolConfig.name" class="form-control" v-model="choiceValue" :disabled="useReadonly()">
                <option :value="choice[choiceValueColumn]" v-for="choice in toolConfig.data">
                    {{ convertLabel(choice[choiceLabelColumn])|trans }}
                </option>
            </select>

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
                    # 指定的key(關聯)
                    if @toolConfig.config.reference != undefined
                        source = @$store.getters.editSetting.dataRow
                        for key in @toolConfig.config.reference.split('.')
                            if source != undefined && source != null
                                if source[key] != undefined && source[key] != null
                                    source = source[key]
                                else
                                    source = undefined
                            else
                                source = undefined
                        return source
                    if Array.isArray @$store.getters.editSetting.dataRow[dataKey]
                        data = @$store.getters.editSetting.dataRow[dataKey]
                        if data[0] == null || data[0] == undefined
                            source = undefined
                        else
                            source = data[0][@toolConfig.config.value]
                    else
                        source = @$store.getters.editSetting.dataRow[dataKey]
                return source
            choiceValueColumn: () ->
                return 'id' if @toolConfig.config.value == undefined
                @toolConfig.config.value
            choiceLabelColumn: () ->
                return 'name' if @toolConfig.config.label == undefined
                @toolConfig.config.label
        mounted: () ->
            @initData()
            @syncData()
            @$root.$on 'form.showError', @handleError
        data: () ->
            choiceValue: null
            errorMsg: ''
            eventName: @$route.name
        props: ['toolConfig']
        methods:
            convertLabel: (label) ->
                return "#{@toolConfig.label_prefix}.#{label}" if @toolConfig.label_prefix != undefined
                label
            handleError: (response) ->
                for key of response
                    if key == @toolConfig.name
                        @errorMsg = response[key]
                        return
                @errorMsg = ''
            syncData: () ->
                @$store.dispatch 'edit.syncData',
                    key: @toolConfig.name
                    deep: @toolConfig.deep
                    repeated: @toolConfig.repeated
                    parent: @toolConfig.parent
                    dataIndex: @toolConfig.dataIndex
                    value: @choiceValue
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
            initData: () ->
                if @data != null && @data != undefined
                    @choiceValue = @data
                    return
                return if @toolConfig.data == undefined
                if @toolConfig.data.length > 0
                    @choiceValue = @toolConfig.data[0].id
                    return
                @choiceValue = null
                return
        watch:
            choiceValue: () ->
                @syncData()
            data:
                deep: true
                handler: () ->
                    @initData()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
select, select > option
    padding: 6px 9px
</style>