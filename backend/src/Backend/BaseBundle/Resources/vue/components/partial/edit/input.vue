<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <input :id="eventName + '_' + toolConfig.name" type="text" class="form-control" :placeholder="usePlaceholder()"
                   v-model="inputText" v-on:keyup="syncData()" v-on:change="syncData()" :readonly="useReadonly()"/>

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
        @$root.$on 'form.beforeSave', @checkRequire
    beforeDestroy: () ->
        @$root.$off 'form.beforeSave', @checkRequire
    data: () ->
        inputText: ''
        errorMsg: ''
        eventName: @$route.name
        readonlyData: ''
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
            else if @toolConfig.repeated == true
                if @toolConfig.parent != undefined
                    source = @$store.getters.editSetting.dataRow[@toolConfig.parent][@toolConfig.dataIndex][dataKey]
                else
                    source = undefined
            else
                source = @$store.getters.editSetting.dataRow
                for node in dataKey.split('.')
                    if source != undefined && source != null
                        source = source[node]
                if @useReadonly() == true
                    source = @$options.filters.trans source
                    source = @readonlyData if source == undefined
                    @readonlyData = source
            return source
    methods:
        checkRequire: (result) ->
            me = @
            me.errorMsg = ''
            result.promises.push new Promise (resolve, reject) ->
                if me.toolConfig.notNull == true
                    if me.inputText == ''
                        me.errorMsg = me.$options.filters.trans 'error.missing_field'
                        reject()
                        me.$root.$emit 'form.enableSubmitButton'

                        me.$store.dispatch 'alert',
                            style: 'error'
                            title: me.$options.filters.trans 'error.save_fail.title'
                            message: me.$options.filters.trans 'error.save_fail.message'
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
        initData: () ->
            if @data != undefined && @data != null
                @inputText = @data
            @syncData @inputText
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
            if @$route.name.match 'new'
                return false
            if @toolConfig.readonly != undefined
                return @toolConfig.readonly
            else
                return false
        syncData: (value) ->
            if value == undefined
                value = @inputText
            @$store.dispatch 'edit.syncData',
                key: @toolConfig.name
                deep: @toolConfig.deep
                repeated: @toolConfig.repeated
                parent: @toolConfig.parent
                dataIndex: @toolConfig.dataIndex
                value: value
    watch:
        data: (value) ->
            @inputText = value

</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>

</style>