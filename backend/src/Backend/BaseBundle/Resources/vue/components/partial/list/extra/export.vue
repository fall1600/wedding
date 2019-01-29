<template>
    <button class="btn btn-info" @click="startExport()">
        <i class="fa fa-refresh fa-spin" v-if="exportStatus"></i>
        <i class="fa fa-file-excel-o" v-if="!exportStatus"></i>
        {{ exportConfig.label | trans }}
    </button>
</template>

<style src="gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css"></style>
<script lang="babel!coffee" type="text/coffeescript">
require 'gentelella/vendors/moment/moment.js'
require 'gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js'
module.exports =
    mounted: () ->
        me = @
        $(@$el).find('.export-date-picker[data-role="start"]').daterangepicker
            singleDatePicker: true
            locale:
                format: 'YYYY/MM/DD'

            (start, end, label) ->
                me.start = end.toISOString()

        $(@$el).find('.export-date-picker[data-role="end"]').daterangepicker
            singleDatePicker: true
            locale:
                format: 'YYYY/MM/DD'

            (start, end, label) ->
                me.end = end.toISOString()
    methods:
        startExport: () ->
            me = @
            @exportStatus = true
            data =
                filter: @filter
            apiName = @api
            for apiNode in @exportConfig.config.api.split('.')
                apiName = apiName[apiNode]
            promise = apiName data, @exportConfig.config.url
            promise.then (result) ->
                me.exportStatus = false
                responseType = result.xhr.getResponseHeader('content-type') || 'application/octet-binary'
                contentHeader = result.xhr.getResponseHeader('content-disposition') || 'attachment; filename="download"'
                regx = /filename=\"(.*?)\"/i
                matches = contentHeader.match regx
                filename = 'download'
                if(matches)
                    filename = matches[1]
                blob = new Blob([result.data], {type: responseType})
                url = URL.createObjectURL blob
                link = "<a id='export-download' href=#{url} download='#{filename}'>link</a>"
                $('body').append(link)
                $('#export-download')[0].click()
                $('#export-download').remove()
            promise.catch () ->
                me.exportStatus = false
                me.$store.dispatch 'alert',
                    style: 'error'
                    title: me.$options.filters.trans 'list.export.fail.title'
                    message: me.$options.filters.trans 'list.export.fail.message'
    data: () ->
        start: new Date().toISOString()
        end: new Date().toISOString()
        exportStatus: false
    computed:
        api: () -> return @$store.state.base.api
        filter: () ->
            _filter = $.extend({}, @$store.getters.filterData)
            delete _filter.page
            return _filter
        exportConfig: () ->
            for extra in @$store.getters.listConfig.extra
                if extra.name == 'export'
                    return extra
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .export-zone
        margin: 10px 10px 0 10px
        .btn
            margin-bottom: 0

    .datepicker
        display: inline-block
        position: relative

        .form-control-feedback
            left: 5px
        input
            width: 150px
</style>