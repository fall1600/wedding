<template>
    <div>
        <a :title="dataToShow">
            {{ trans(simpliyText(dataToShow)) }}
        </a>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['position', 'dataEditable', 'dataType'],
    mounted: () ->
        @$root.$on 'quick-edit-list-item',  @editable
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
    methods:
        simpliyText: (text) ->
            secret = ''
            for i in [1..text.length - 5]
                secret += '*'
            lastFive = text.slice(-5)
            return "#{lastFive}"
        trans: (label) ->
            if @listConfig.config[@position.key] != undefined && @listConfig.config[@position.key]["label_prefix"] != undefined
                label = "#{@listConfig.config[@position.key]["label_prefix"]}.#{label}"
            if @listConfig.config[@position.key] != undefined && @listConfig.config[@position.key]["label_postfix"] != undefined
                label = "#{label}.#{@listConfig.config[@position.key]["label_postfix"]}"
            @$options.filters.trans label
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
</style>