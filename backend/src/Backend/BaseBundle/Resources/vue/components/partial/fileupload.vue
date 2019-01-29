<template>
    <div class="upload-manage">
        <button class="btn btn-info" v-on:click="startUpload">
            <i class="fa fa-upload"></i>
            <span>
                開始上傳
            </span>
        </button>

        <form class="dropzone"></form>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
Dropzone = require 'gentelella/vendors/dropzone/dist/dropzone.js'
module.exports =
    props: ['config']
    mounted: () ->
        @setupDropzone()
    data: () ->
        photoUpload: []
    methods:
        startUpload: () ->
            me = @

            if me.photoUpload.files.length != 0
                # 開啟自動上傳, 否則一次只能傳兩筆
                me.photoUpload.options.autoProcessQueue = true
                me.photoUpload.processQueue()
            else
                me.$store.dispatch "alert",
                    style: "warning"
                    title: "沒有檔案"
                    message: "請選擇檔案！"
        setupDropzone: () ->
            me = @
            Dropzone.autoDiscover = false

            $ () ->
                # init
                me.photoUpload = new Dropzone('.dropzone', {
                    url: me.config.uploadUrl
                    dictDefaultMessage: '請將圖片拖曳到方格中, 或是直接點選方格選取檔案'
                    acceptedFiles: me.config.acceptType
                    dictInvalidFileType: '檔案格式不符'
                    addRemoveLinks: true
                    dictRemoveFile: '刪除檔案'
                    autoProcessQueue: false
                })

                # 取得base64格式縮圖
                me.photoUpload.on 'thumbnail', (file, dataUrl) ->

                # 取得處理百分比
                me.photoUpload.on 'totaluploadprogress', (total) ->
                    # console.log "#{total}%"

                # 取得成功的callback
                me.photoUpload.on 'success', (file, responseText) ->
                    # console.log "success: #{responseText}"
                    me.$store.dispatch "alert",
                        style: "success"
                        title: "上傳成功！"
                        message: "圖片#{file.name}上傳成功！"

                # 取得失敗的callback
                me.photoUpload.on 'error', (file, responseText) ->
                    if responseText == '檔案格式不符'
                        me.$store.dispatch "alert",
                            style: "error"
                            title: "警告"
                            message: "#{file.name}檔案格式不符, 已移除！"
                    else
                        me.$store.dispatch "alert",
                            style: "error"
                            title: "警告"
                            message: "圖片#{file.name}上傳失敗！"

                # 新增檔案到佇列事件
                me.photoUpload.on 'complete', (file) ->
                    # 如果格式不符就刪除
                    if file.accepted == false
                        me.photoUpload.removeFile(file)

                # 上傳完成後
                me.photoUpload.on 'queuecomplete', () ->

                    # 關閉自動上傳
                    me.photoUpload.options.autoProcessQueue = false

                    # dispatch上傳完成事件
                    me.$store.dispatch(me.config.completeDispatch, me.config.completeDispatchData)
</script>

<style src="gentelella/vendors/dropzone/dist/dropzone.css"></style>
<style lang="sass?indentedSyntax" type="text/sass">
    .upload-manage
        .dropzone
            min-height: 200px !important
            margin-bottom: 30px
</style>