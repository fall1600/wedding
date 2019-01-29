<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)" v-if="checkboxGroup.length != 0">
            <p class="help-block">{{ useHelp()|trans }}</p>
            <div class="errorMsg text-danger" v-if="errorMsg != ''">
                <h5>
                    <i class="fa fa-warning"></i>
                    {{ errorMsg|trans }}
                </h5>
            </div>

            <div class="checkbox-container row">
                <div class="col-md-6" v-for="(group, groupIndex) in checkboxGroup">
                    <table class="table checkbox-table">
                        <thead>
                            <tr class="active">
                                <th>
                                    <label>{{trans(group.name, 'group_label')}}</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(option, optionIndex) in group.child" :checkbox-group-index="groupIndex">
                                <td-checkbox :position="{groupIndex:groupIndex, optionIndex:optionIndex}" :dataKey="toolConfig.name"></td-checkbox>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    components:
        'td-checkbox': require './checkboxgroup/checkbox.vue'
    mounted: () ->
        @$root.$on 'form.showError', @handleError
        @checkboxGroup = @toolConfig.data
        @$store.dispatch 'checkboxgroup.update.list', @toolConfig.data
        @initData()
    watch:
        checkboxGroup:
            deep: true
            handler: () ->
                @initData()
    props: ['toolConfig']
    data: () ->
        errorMsg: ''
    methods:
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
        trans: (label, labelType) ->
            if @toolConfig.config["#{labelType}_prefix"] != undefined
                label = "#{@toolConfig.config["#{labelType}_prefix"]}.#{label}"
            if @toolConfig.config["#{labelType}_postfix"] != undefined
                label = "#{label}.#{@toolConfig.config["#{labelType}_postfix"]}"
            @$options.filters.trans label
        initData: () ->
            if @data == undefined
                @$store.dispatch 'edit.syncData',
                    key: @toolConfig.name
                    value: []
    computed:
        checkboxGroup: () ->
            @$store.getters.checkboxGroup
        uniqueId: () ->
            originalName = @toolConfig.name.replace '.', '-'
            return "#{originalName}_#{new Date().getTime()}"
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
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
.checkbox-table
    border: 1px solid #ddd
</style>