<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <select :data-select2-id="uniqueId" class="form-control" multiple="multiple">
                <optgroup v-for="optgroup in listContent" :label="trans(optgroup[choiceGroupLabelColumn], 'group_label')">
                    <option v-for="option in optgroup[choiceGroupChildColumn]" :value="option.id">
                        {{ trans(option.name, 'label') }}
                    </option>
                </optgroup>
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
require 'select2/dist/js/select2.js'
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    mounted: () ->
        @$root.$on 'form.showError', @handleError
        me = @
        # 取得資料render出來後, 才設定selector2 plugin
        new Promise (resolve, reject) ->
            me.listContent = me.toolConfig.data
            me.startRender = true
            resolve()
        .then (result) ->
            me.setSelector()
    props: ['toolConfig']
    watch:
        listContent:
            deep:  true
            handler: (oldValue, newValue) ->
#                @setSelector()
    data: () ->
        listContent: [],
        startRender: false
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
        setData: () ->
#            將資料陣列的資料做選取
            $("[data-select2-id=#{@uniqueId}]").val(@selectedDataArray).trigger('change')
        # 設定多選單
        setSelector: () ->
            me = @
            selector = "[data-select2-id=#{@uniqueId}]"

            $(selector).select2({
                placeholder: @trans @toolConfig.text
            })

            @setData()

            # 更動資料
            $(selector).on 'change', () ->
                me.syncData()
        syncData: () ->
            me = @
            selector = "[data-select2-id=#{@uniqueId}]"
            newData = []
            source = $(selector).val()
            for item in source
                newData.push {id: item.trim()}
            # 同步資料
            me.$store.dispatch 'edit.syncData',
                key: me.toolConfig.name
                deep: me.toolConfig.deep
                repeated: @toolConfig.repeated
                parent: @toolConfig.parent
                dataIndex: @toolConfig.dataIndex
                value: newData
    computed:
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
        choiceGroupLabelColumn: () ->
            return 'name' if @toolConfig.config.group_label == undefined
            @toolConfig.config.group_label
        choiceGroupChildColumn: () ->
            return 'child' if @toolConfig.config.group_child == undefined
            @toolConfig.config.group_child
        choiceValueColumn: () ->
            return 'id' if @toolConfig.config.value == undefined
            @toolConfig.config.value
        choiceLabelColumn: () ->
            return 'name' if @toolConfig.config.label == undefined
            @toolConfig.config.label
        # 要選擇的歷史資料陣列
        selectedDataArray: () ->
            return [] if @data == undefined
            dataArray = []
            for selected in @data
                if selected != undefined
                    dataArray.push selected.id
            return dataArray
    updated: () ->
        me = @
        setTimeout () ->
            me.setData()
        ,1000
</script>

<style src="select2/dist/css/select2.css"></style>
<style lang="sass?indentedSyntax" type="text/sass" scoped>

</style>