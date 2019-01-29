<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <select :data-select2-id="uniqueId" class="form-control" multiple="multiple">
                <option v-for="option in listContent" :value="option[choiceValueColumn]">
                    {{ trans(option[choiceLabelColumn]) }}
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
require 'select2/dist/js/select2.js'
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    props: ['toolConfig']
    watch:
        listContent:
            deep:  true
            handler: (oldValue, newValue) ->
                @setSelector()
    data: () ->
        errorMsg: ''
    mounted: () ->
        @$root.$on 'form.showError', @handleError
        @$root.$on 'form.beforeSave', @checkRequire
        @setSelector()
        return
    beforeDestroy: () ->
        @$root.$off 'form.beforeSave', @checkRequire
    methods:
        checkRequire: (result) ->
            me = @
            me.errorMsg = ''
            selector = "[data-select2-id=#{@uniqueId}]"
            source = $(selector).val()

            result.promises.push new Promise (resolve, reject) ->
                if me.toolConfig.notNull == true
                    if source == undefined
                        me.errorMsg = me.$options.filters.trans 'error.missing_field'
                        reject()
                        me.$root.$emit 'form.enableSubmitButton'

                        me.$store.dispatch 'alert',
                            style: 'error'
                            title: me.$options.filters.trans 'error.save_fail.title'
                            message: me.$options.filters.trans 'error.save_fail.message'
                    else
                        if source.length == 0
                            me.errorMsg = me.$options.filters.trans 'error.missing_field'
                            reject()
                            me.$root.$emit 'form.enableSubmitButton'
                        else
                            resolve()
                else
                    resolve()
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
        trans: (label) ->
            if @toolConfig.config["label_prefix"] != undefined
                label = "#{@toolConfig.config["label_prefix"]}.#{label}"
            if @toolConfig.config["label_postfix"] != undefined
                label = "#{label}.#{@toolConfig.config["label_postfix"]}"
            @$options.filters.trans label
        setData: () ->
#            將資料陣列的資料做選取
            $("[data-select2-id=#{@uniqueId}]").val(@selectedDataArray).trigger('change')
            @syncData()
        useReadonly: () ->
            if @toolConfig.readonly != undefined
                return @toolConfig.readonly
            else
                return false
        # 設定多選單
        setSelector: () ->
            return if @listContent == null
            me = @
            selector = "[data-select2-id=#{@uniqueId}]"
            $(selector).select2({
                placeholder:  @trans me.toolConfig.text
            })

            if(@useReadonly())
                $(selector).prop("disabled", true)
            @setData()

            $(selector).on 'change', () ->
                me.syncData()
        syncData: () ->
            me = @
            selector = "[data-select2-id=#{@uniqueId}]"
            newData = []
            source = $(selector).val()

            return if source == undefined
            for item in source
                if @toolConfig.dataType == 'string_array'
                    newData.push item.trim()
                else
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
        choiceValueColumn: () ->
            return 'id' if @toolConfig.config.value == undefined
            @toolConfig.config.value
        choiceLabelColumn: () ->
            return 'name' if @toolConfig.config.label == undefined
            @toolConfig.config.label
        listContent: () ->
            return null if @toolConfig.data == undefined
            if @toolConfig.config != undefined
                if @toolConfig.config.removeRoot == true
                    @toolConfig.data.shift()
            return @toolConfig.data
        # 要選擇的歷史資料陣列
        selectedDataArray: () ->
            return [] if @data == undefined
            dataArray = []
            for selected in @data
                dataArray.push selected.id
            return dataArray
</script>

<style src="select2/dist/css/select2.css"></style>
<style lang="sass?indentedSyntax" type="text/sass">
span.select2-container--disabled
  &>.selection
    cursor: not-allowed
    &>.select2-selection--multiple
      background-color: white
      &.select2-selection__rendered
        cursor: not-allowed
</style>
