<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))" :for="eventName + '_' + toolConfig.name">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <input :id="eventName + '_' + toolConfig.name" type="text" class="form-control has-feedback-left date-picker" :placeholder="usePlaceholder()" :disabled="useReadonly(toolConfig.text)"/>
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
        time: null
    computed:
        format: () ->
            return 'YYYY-MM-DD HH:mm:ss' if !@toolConfig
            return 'YYYY-MM-DD HH:mm:ss' if !@toolConfig.format
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
            return source
        editSetting: () ->
            return @$store.getters.editSetting
        content: () ->
            if @data == undefined || @data == null
                return moment(new Date()).format(@format)
            else
                return moment(@data).format(@format)
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
        useReadonly: () ->
            if @toolConfig.readonly == true
                return true
            else
                return false
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
                timePickerSeconds: true
                timePicker24Hour: true
                startDate: me.content
                timePicker: true
                timePickerIncrement: 1
                locale:
                    format: @format
                (start, end, label) ->
                    result = moment(end.toISOString()).format(me.format)
                    me.syncData(result)
        syncData: (date) ->
            @$store.dispatch 'edit.syncData',
                key: @toolConfig.name
                deep: @toolConfig.deep
                repeated: @toolConfig.repeated
                parent: @toolConfig.parent
                dataIndex: @toolConfig.dataIndex
                value: date
    watch:
        data:
            deep: true
            handler: () ->
                @setDate()
</script>

<style lang="sass?indentedSyntax" type="text/sass">
div.daterangepicker_input
    div.calendar-time
        select
            width: 60px
</style>