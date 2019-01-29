<template>
    <div class="form-group" :class="setGrid(toolConfig.grid)">
        <label class="control-label" :class="mergeClass(useRequired(), labelGrid(toolConfig.labelGrid))">
            {{ toolConfig.text|trans }}
        </label>
        <div :class="toolGrid(toolConfig.toolGrid)">
            <div class="img-history-group" v-if="data != undefined">
                <h5 v-if="data.photos != null || data.photos != undefined">
                    <span v-if="data.photos.length != 0">
                        {{'form.label.edit.upload.uploaded'|trans}}
                    </span>
                </h5>

                <div v-if="data != undefined ">
                    <div class="img-container" v-for="(photo, index) in data.photos" v-if="showUploaded && data.photos != undefined">
                        <span class="delete-btn btn btn-xs btn-danger" @click="deleteFile(index)" v-if="!useReadonly()">
                            <i class="fa fa-close"></i>
                        </span>
                        <img :src="photo[converKey]">
                    </div>
                </div>
            </div>

            <div class="img-upload-zone" v-if="!useReadonly()">
                <h5>
                    {{'form.label.edit.upload.new'|trans}}
                </h5>

                <input type="file" class="imageFiles form-contorl" multiple="multiple" accept="image/*">
            </div>

            <div class="preview-zone" v-if="preview.length != 0">
                <h5>
                    {{'form.label.edit.upload.view'|trans}}
                </h5>

                <div class="preview-image" v-for="(image, index) in preview">
                    <div class="image-container">
                        <img :src="image.src">
                    </div>

                    <div class="tools">
                        <button class="btn btn-danger btn-sm" type="button" @click="deleteFileQueue(index)">
                            <i class="fa fa-trash"></i>
                            <span>
                            {{'form.label.edit.upload.delete'|trans}}
                        </span>
                        </button>
                    </div>

                    <div class="progress">
                        <div v-if="image.progress != 'fail'" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
                             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" :style="'width: '+image.progress+'%'">
                            <span class="sr-only"></span>
                        </div>

                        <div v-if="image.progress == 'fail'" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar"
                             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only"></span>
                        </div>
                    </div>

                    <div class="status">
                        <span v-if="image.progress != 100 && image.progress != 'fail'">
                            {{'form.label.edit.upload.queue'|trans}}
                        </span>

                        <span v-if="image.progress != 'fail' && image.progress == 100">
                            {{'form.label.edit.upload.success'|trans}}
                        </span>

                        <span v-if="image.progress == 'fail'">
                            {{'form.label.edit.upload.fail'|trans}}
                        </span>
                    </div>
                </div>
            </div>

            <p class="help-block" v-if="!useReadonly()">
                {{ 'form.label.edit.upload.upload.limit.max'| trans}}: {{ toolConfig.config.max }},

                {{ 'form.label.edit.upload.upload.limit.min'| trans}}: {{ toolConfig.config.min }}
            </p>
            <p>{{help| trans}}</p>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
promiseSeries = require 'promise.series'
module.exports =
    mixins: [require "components/backendbase/mixins/formComponent.coffee"]
    props: ['toolConfig']
    data: () ->
        preview: []
        converKey: 'large'
        showUploaded: true
        fileData: []
    computed:
        help: () ->
            return null if !@toolConfig.config
            return @toolConfig.config.help
        editSetting: () ->
            return @$store.getters.editSetting
        queueImages: () ->
            return @$store.getters.queueImages
        data: () ->
            return @editSetting.dataRow[@toolConfig.name]
        editModel: () ->
            return @$store.getters.editModel
        api: () ->
            @$store.state.base.api
    created: () ->
        @initData()
    updated: () ->
        @initData()
    mounted: () ->
        me = @
        $(@$el).find('.imageFiles').click (event) ->
            $(this).val('')

        $(@$el).find('.imageFiles').change (event) ->
            preview = []
            fileList = event.target.files

            if fileList.length != 0
                for index in [0..fileList.length-1]
                    file = fileList[index]
                    reader = new FileReader()
                    reader.onload = ((file) ->
                        return (event) ->
                            image =
                                title: escape file.name
                                src: event.target.result
                                index: index
                                progress: 0
                            me.preview.push image
                            me.fileData.push file
                    )(file)
                    reader.readAsDataURL(file)
        @$root.$on 'form.beforeSave', @fileupload
    methods:
        useReadonly: () ->
            if @toolConfig.readonly != undefined
                return @toolConfig.readonly
            else
                return false
        initData: () ->
            if !(@data instanceof Object)
                initFormat =
                    master: {}
                    photos: []
                @$set @editSetting.dataRow, "#{@toolConfig.name}", initFormat
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
        deleteFileQueue: (index) ->
            @fileData.splice index, 1
            @preview.splice index, 1
        deleteFile: (index) ->
            me = @
            if confirm me.$options.filters.trans 'form.label.edit.upload.delete_confirm'
                @$store.dispatch 'edit.delete.photo',
                    key: @toolConfig.name
                    index: index
#        檢查限制數量, mode為alert時, 不符合限制會彈出警告
        checkLimit: (min, max, mode) ->
            me = @
            dataNumber = 0
            for photo in @fileData
                dataNumber++
            if @fileData.length == undefined
                dataNumber = 0
            if @data != null
                dataNumber += @data.photos.length
            if  dataNumber < min || dataNumber > max
                if mode == 'alert'
                    me.$store.dispatch 'alert',
                        style: 'error'
                        title: me.$options.filters.trans 'form.label.edit.upload.upload.not_allow.title'
                        message: me.$options.filters.trans 'form.label.edit.upload.upload.not_allow.message'
                return false
            else
                return true
            return false
        fileupload: (result) ->
            event =
                configType: @toolConfig.config.configType
                '$el': @$el
            @$root.$emit 'before.photo.upload', event
            configType = event.configType
            me = @
            return if $(me.$el).is(':visible') == false
            me.checkLimit(me.toolConfig.config.min, me.toolConfig.config.max, 'alert')
            if me.checkLimit(me.toolConfig.config.min, me.toolConfig.config.max) == false
                result.promises.push new Promise (mainResolve, mainReject) ->
                    mainReject()
                    me.$root.$emit 'form.enableSubmitButton'
            if me.checkLimit(me.toolConfig.config.min, me.toolConfig.config.max) != false
                result.promises.push new Promise (mainResolve, mainReject) ->
                    me.showUploaded = false
                    uploadPromises = []
                    successFile = []
                    failFile = 0
                    fileNumber = me.fileData.length

                    for index in [0..fileNumber-1]
                        uploadPromises.push do (index) ->
                            () ->
                                photo = new FormData()
                                return if me.fileData.length == 0
                                photo.append "photo", me.fileData[index]

                                promise = me.api.multipartRequest "/photo/#{configType}", photo,
                                    (progress) ->
                                        if fileNumber != 0 && me.checkLimit(me.toolConfig.config.min, me.toolConfig.config.max) == true
                                            percentage = Math.floor(100*(progress.loaded/progress.total))
                                            if me.preview[index] != undefined
                                                me.preview[index].progress = percentage

                                promise.then (result) ->
                                    me.$store.dispatch 'edit.update.photo',
                                        key: me.toolConfig.name
                                        photo: result
                                    successFile.push index
                                .catch (reason) ->
                                    me.preview[index].progress = 'fail'
                                    failFile++
                    promiseSeries(uploadPromises)
                    .then () ->
                        mainResolve()
                        me.showUploaded = true
                        me.fileData = []
                        me.preview = []
                        if failFile == 0 && successFile.length != 0
                            me.$store.dispatch 'alert',
                                style: 'success'
                                title: me.$options.filters.trans 'form.label.edit.upload.upload.success.title'
                                message: "#{me.$options.filters.trans me.toolConfig.text} #{successFile.length} #{me.$options.filters.trans 'form.label.edit.upload.upload.success.message'}"
                        if failFile != 0
                            me.$store.dispatch 'alert',
                                style: 'error'
                                title: me.$options.filters.trans 'form.label.edit.upload.upload.fail.title'
                                message: "#{me.$options.filters.trans me.toolConfig.text} #{failFile} #{me.$options.filters.trans 'form.label.edit.upload.upload.fail.message'}"
                    .catch () ->
                        mainReject()
                        me.showUploaded = true
                        me.fileData = []
                        me.preview = []
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    $padding: 20px
    $imageSize: 100px
    .img-history-group
        padding: $padding

        .img-container
            margin: 0 20px 0 20px
            display: inline-block
            position: relative
            width: $imageSize
            height: $imageSize
            overflow: hidden

            .delete-btn
                position: absolute
                right: 0
                margin: 0
                opacity: 0.8
                z-index: 200
                &:hover
                    opacity: 1

            img
                z-index: 100
                width: $imageSize
                position: absolute
    .tools
        .btn
            margin: 10px 0 10px 0
            width: 100%
    .img-upload-zone
        padding: 0 $padding 0 $padding

    .preview-zone
        padding: $padding

        .preview-image
            margin: 0 20px 0 20px
            display: inline-block

            width: $imageSize
            height: $imageSize

            .status
                text-align: center

            .image-container
                position: relative
                width: $imageSize
                height: $imageSize

                img
                    width: $imageSize
                    position: absolute
</style>