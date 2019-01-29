<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <input :id="eventName + '_' + toolConfig.name" :readonly="useReadonly()" type="text" class="form-control has-feedback-left date-picker" :placeholder="usePlaceholder()"/>
            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>

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

<style src="gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css"></style>
<script lang="babel!coffee" type="text/coffeescript">
require 'gentelella/vendors/moment/moment.js'
require 'gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js'
moment = require 'moment'
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    mounted: () ->
        @setDate()
        @$root.$on 'form.showError', @handleError
    props: ['toolConfig']
    data: () ->
        errorMsg: ''
        eventName: @$route.name
        hasSetDefaul: false
    computed:
        format: () ->
            return 'YYYY-MM-DD' if !@toolConfig
            return 'YYYY-MM-DD' if !@toolConfig.format
            return @toolConfig.format
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
                if isNaN(Date.parse(source)) == true && @toolConfig.default != undefined && @hasSetDefaul == false
                    source = @setDefault @toolConfig.default
                source = @setDefault @toolConfig.default if @hasSetDefaul
            return source
        editSetting: () ->
            return @$store.getters.editSetting
        content: () ->
            if @data == undefined || @data == null
                current = new Date()
                return moment(new Date()).format(@format)
            else
                return moment(@data).format(@format)
    methods:
        useReadonly: () ->
            if @toolConfig.readonly == true
                return true
            else
                return false
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
        setDate: () ->
            me = @
            me.syncData(me.content)
            $(@$el).find('.date-picker').daterangepicker
                singleDatePicker: true
                startDate: me.content
                locale:
                    format: 'YYYY-MM-DD'
                (start, end, label) ->
                    result = moment(end.toISOString()).format(me.format)
                    me.syncData(result)
        syncData: (date) ->
            me = @
            @$store.dispatch 'edit.syncData',
                key: me.toolConfig.name
                deep: me.toolConfig.deep
                repeated: me.toolConfig.repeated
                parent: me.toolConfig.parent
                dataIndex: me.toolConfig.dataIndex
                value: date
        setDefault: (config) ->
            @hasSetDefaul = true
            t = new Date()
            if config.unit == 'year'
                if config.type == 'increment'
                    today = "#{t.getFullYear()+config.value}-#{t.getMonth()+1}-#{t.getDate()}"
                if config.type == 'decrement'
                    today = "#{t.getFullYear()-config.value}-#{t.getMonth()+1}-#{t.getDate()}"
            if config.unit == 'day'
                if config.type == 'increment'
                    today = "#{t.getFullYear()}-#{t.getMonth()+1}-#{t.getDate()+config.value}"
                if config.type == 'decrement'
                    today = "#{t.getFullYear()}-#{t.getMonth()+1}-#{t.getDate()-config.value}"
            return today
    watch:
        data:
            deep: true
            handler: () ->
                @setDate()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>

</style>