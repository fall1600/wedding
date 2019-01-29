<template>
    <button v-if="moduleName != null" class="btn btn-info" @click="startDownload()">
        {{label|trans}}
    </button>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    methods:
        startDownload: () ->
            me = @
            api = @api[@moduleName][@actionColumn.source]
            promise = api @dataId
            promise.then (result) ->
                responseType = result.xhr.getResponseHeader('content-type') || 'application/octet-binary'
                contentHeader = result.xhr.getResponseHeader('content-disposition') || 'attachment; filename="download"'
                regx = /filename=\"(.*?)\"/i
                matches = contentHeader.match regx
                filename = 'download'
                if(matches)
                    filename = matches[1]
                blob = new Blob([result.data], {type: responseType})
                url = URL.createObjectURL blob
                randomId = "download-#{Math.floor(Math.random()*1000000)}"
                link = "<a id='#{randomId}' href=#{url} download='#{filename}'>link</a>"
                $('body').append(link)
                $("##{randomId}")[0].click()
                $("##{randomId}").remove()
            promise.catch () ->
                me.$store.dispatch 'alert',
                    style: 'error'
                    title: me.$options.filters.trans 'list.export.fail.title'
                    message: me.$options.filters.trans 'list.export.fail.message'
    props:  ["actionColumn", "dataId"]
    computed:
        moduleName: () ->
            return null if @actionColumn.module == undefined
            @actionColumn.module
        api: () ->
            return @$store.state.base.api
        label: () ->
            return "action.download" if @actionColumn.label == undefined
            @actionColumn.label
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