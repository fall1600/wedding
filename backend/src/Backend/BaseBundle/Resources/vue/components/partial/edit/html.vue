<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">

            <div class="progress" :class="{ active: uploading }">
                <div class="progress-bar progress-bar-striped" role="progressbar" :class="progressStyle"
                     aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" :style="'width:'+progress+'%'">
                    <span class="sr-only"></span>
                </div>
            </div>

            <textarea :id="editorId" class="editor-wrapper" v-if="!useReadonly()">{{data}}</textarea>
            <div v-html="data" v-if="useReadonly()"></div>

            <p class="help-block">{{ useHelp()|trans }}</p>
            <div class="errorMsg text-danger" v-if="errorMsg != ''">
                <h5>
                    <i class="fa fa-warning"></i>
                    {{ errorMsg|trans }}
                </h5>
            </div>
        </div>
        <input type="file" class="ed-file" :id="fileinputId" :accept="accept">
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
lang = navigator.language || navigator.userLanguage
lang = lang.replace('-', '_')
require 'tinymce/tinymce'
require 'tinymce/themes/modern/theme'
require 'tinymce/plugins/paste/plugin'
require 'tinymce/plugins/link/plugin'
require 'tinymce/plugins/autoresize/plugin'
require 'tinymce/plugins/image/plugin'
require 'tinymce/plugins/media/plugin'
require 'tinymce/plugins/table/plugin'
require 'tinymce/plugins/code/plugin'
require 'tinymce/plugins/advlist/plugin'
require 'tinymce/plugins/autolink/plugin'
require 'tinymce/plugins/lists/plugin'
require 'tinymce/plugins/charmap/plugin'
require 'tinymce/plugins/print/plugin'
require 'tinymce/plugins/preview/plugin'
require 'tinymce/plugins/anchor/plugin'
require 'tinymce/plugins/searchreplace/plugin'
require 'tinymce/plugins/visualblocks/plugin'
require 'tinymce/plugins/fullscreen/plugin'
require 'tinymce/plugins/insertdatetime/plugin'
require 'tinymce/plugins/contextmenu/plugin'
require 'tinymce/plugins/textcolor/plugin'
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    mounted: () ->
        me = @
        @setEditor()
        @$root.$on 'form.showError', @handleError
    props: ['toolConfig']
    computed:
        api: () ->
            @$store.state.base.api
        # 設定綁定的id, 避免重複的元件互相影響
        editorId: () ->
            originalName = @toolConfig.name.replace '.', '-'
            rename = "#{originalName}_#{new Date().getTime()}"
            return "#{rename}-html-editor"
        fileinputId: () ->
            return "#{@editorId}-file"
        fileinputTarget: () ->
            return "##{@editorId}-file"
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

            if source == undefined
                @$store.dispatch 'edit.syncData',
                    key: @toolConfig.name
                    deep: @toolConfig.deep
                    repeated: @toolConfig.repeated
                    parent: @toolConfig.parent
                    dataIndex: @toolConfig.dataIndex
                    value: ''
                return ''
            else
                return source
        accept: () ->
            if @toolConfig.config != undefined
                if @toolConfig.config.uploadAccept != undefined
                    return @toolConfig.config.uploadAccept
            return 'image/*'
    data: () ->
        errorMsg: ''
        uploading: false
        progress: 0
        progressStyle: 'progress-bar-success'
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
        useReadonly: () ->
            if @toolConfig.readonly != undefined
                return @toolConfig.readonly
            else
                return false
        setEditor: () ->
            me = @
            # 設定編輯器ID, 關閉預設的快捷鍵
            tinymce.init
                resize: 'both'
                selector: "##{@editorId}"
                skin: false
                file_picker_types: 'image'
                min_height: 450
                language: lang
                language_url: "/static/langs/#{lang}.js"
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code table'
                    'textcolor'
                ]
                image_title: true
                file_picker_callback: (cd, value, meta) ->
                    $(me.fileinputTarget).click()
                toolbar: 'code | undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | link image | forecolor backcolor | fullscreen'
                menubar: false
                setup:  (ed) ->
                    ed.on 'blur', (e) ->
                        _content = ed.getContent()
                        _content = _content.replace /<img/g, '<img class="img-responsive"'
                        ed.setContent _content
                        me.$store.dispatch 'edit.syncData',
                            key: me.toolConfig.name
                            deep: me.toolConfig.deep
                            repeated: me.toolConfig.repeated
                            parent: me.toolConfig.parent
                            dataIndex: me.toolConfig.dataIndex
                            value: _content

            $(me.fileinputTarget).change (event) ->
                me.progress = 0
                me.progressStyle = 'progress-bar-success'
                $('.mce-close').click()
                fileList = event.target.files
                formData = new window.FormData()
                formData.append('file', fileList[0])

                me.uploading = true
                promise = me.api.multipartRequest "/photo/default", formData,
                    (progress) ->
                        percentage = Math.floor(100*(progress.loaded/progress.total))
                        me.progress = percentage
                promise.then (result) ->
                    $(me.fileinputTarget).val('')
                    me.uploading = false
                    me.progress = 0
                    uid = result._uid
                    url = result.large
                    for info, index in result.large.split(uid)[1].split('.')
                        suffix = info if index == 1
                        ext = info.split('?')[0] if index == 2
                    imageTag = "<img src=#{url} class='img-responsive' data-type='img' data-uid=#{uid} data-suffix=#{suffix} data-ext=#{ext} />"

                    # 插入image tag
                    tinymce.execCommand('mceInsertContent',false, imageTag)

                    me.$store.dispatch 'alert',
                        style: 'success'
                        title: me.$options.filters.trans 'editor.upload.success.title'
                        message: me.$options.filters.trans 'editor.upload.success.message'
                promise.catch (reason) ->
                    me.uploading = false
                    me.progress = 0
                    $(me.fileinputTarget).val('')
                    me.progressStyle = 'progress-bar-danger'
                    me.$store.dispatch 'alert',
                        style: 'error'
                        title: me.$options.filters.trans 'editor.upload.error.title'
                        message: me.$options.filters.trans 'editor.upload.error.message'
</script>

<style src="tinymce/skins/lightgray/skin.min.css"></style>
<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .ed-file
        opacity: 0
        height: 0px

    .progress
        height: 10px
        margin-bottom: 0
        visibility: hidden
        &.active
            visibility: visible
</style>