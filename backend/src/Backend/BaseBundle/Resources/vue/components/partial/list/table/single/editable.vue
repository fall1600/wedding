<template>
    <div>
        <div v-if="!isEditable">
            {{ trans(dateFilter(dataToShow)) }}
        </div>
        <div v-if="isEditable">
            <input v-bind:type="dataType" :value="dataToShow" class="dataToEdit" v-bind:class="{ active: isActive }" @keyup.enter="save()">
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['position', 'dataEditable', 'dataType'],
    mounted: () ->
        @$root.$on 'list.quickedit',  @editable
        @$root.$on 'list.quickedit.cancel', @rollbackValue
        @originValue = @singleData[@position.key] if @dataEditable
    beforeDestroy: () ->
        @$root.$off 'list.quickedit'
        @$root.$off 'list.quickedit.cancel'
    computed:
        singleData: () ->
            return @$store.getters.listData[@position.row]
        listConfig: () ->
            return @$store.getters.listConfig
        dataToShow: () ->
            data = @singleData
            keys = @position.key.split('.')
            for key in keys
                return '' if data[key] == undefined || data[key] == null
                data = data[key]
            return data
    data: () ->
        isEditable: false,
        isActive: false
        originValue: null
    methods:
        save: () ->
            @$root.$emit 'list.trigger.quickedit', @singleData.id
        trans: (label) ->
            if @listConfig.config[@position.key] != undefined && @listConfig.config[@position.key]["label_prefix"] != undefined
                label = "#{@listConfig.config[@position.key]["label_prefix"]}.#{label}"
            if @listConfig.config[@position.key] != undefined && @listConfig.config[@position.key]["label_postfix"] != undefined
                label = "#{label}.#{@listConfig.config[@position.key]["label_postfix"]}"
            if @listConfig.config[@position.key] != undefined
                if @listConfig.config[@position.key].replace != undefined
                    regPattern = new RegExp("#{@listConfig.config[@position.key].replace.match}")
                    if regPattern.test(label) == true
                        label = @listConfig.config[@position.key].replace.newString
            @$options.filters.trans label
        # 啟用編輯
        editable: (rowIndex) ->
            me = @
            # 如果資料不可編輯, 將不會啟用編輯模式
            if @dataEditable == false
                return

            # 如果是停用編輯, 將編輯啟用
            if @isEditable == false
                if @position.row == rowIndex
                    me.isEditable = !me.isEditable

            # 如果是啟用編輯, 將編輯停用, 並且廣播檔案更新事件
            else
                if(@position.row == rowIndex && $(@$el).find('.dataToEdit').hasClass('active'))
                    me.isEditable = !me.isEditable
                    data = $(@$el).find('.dataToEdit').val()
                    @originValue = data
                    @$store.dispatch 'list.syncData',
                        row: @position.row
                        key: @position.key
                        value: data
        rollbackValue: (rowIndex) ->
            return if @dataEditable == false
            if @isEditable == false
                if @position.row == rowIndex
                    @isEditable = !@isEditable
            if(@position.row == rowIndex && $(@$el).find('.dataToEdit').hasClass('active'))
                @isEditable = !@isEditable

                @$store.dispatch 'list.syncData',
                    row: @position.row
                    key: @position.key
                    value: @originValue
        dateFilter: (data) ->
            if @dataType == 'datetime-local'
                return '' if isNaN(Date.parse(data))
                return @timeFormat(data, 'datetime')
            if @dataType == 'date'
                return @timeFormat(data, 'date')
            if @dataType == 'time'
                return @timeFormat(data, 'time')
            else
                return data

        timeFormat: (time, format) ->
            t = new Date (time)
            y = t.getFullYear()
            m = t.getMonth()+1
            d = t.getDate()
            h = t.getHours()
            i = t.getMinutes()
            s = t.getSeconds()

            if m < 10
                m = "0#{m}"
            if d < 10
                d = "0#{d}"
            if h < 10
                h = "0#{h}"
            if i < 10
                i = "0#{i}"
            if s < 10
                s = "0#{s}"
            switch format
                when 'datetime-local'
                    return "#{y}-#{m}-#{d}T#{h}:#{i}"
                when 'datetime'
                    return "#{y}-#{m}-#{d} #{h}:#{i}:#{s}"
                when 'date'
                    return "#{y}-#{m}-#{d}"
                when 'time'
                    return "#{h}:#{i}:#{s}"

    watch:
        isEditable: () ->
            @isActive = @isEditable
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
</style>