<template>
    <div>
        <a :title="dataToShow">
            {{ short(dataToShow) }}
        </a>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['position', 'dataType'],
    mounted: () ->
        @$root.$on 'quick-edit-list-item',  @editable
    computed:
        singleData: () -> @$store.getters.listData[@position.row]
        listConfig: () -> @$store.getters.listConfig
        config: () -> @listConfig.config[@position.key]
        dataToShow: () ->
            data = @singleData
            keys = @position.key.split('.')
            for key in keys
                return '' if data[key] == undefined || data[key] == null
                data = data[key]

            string = ''
            for item, index in data
                string = "#{item[@config.labelKey]}" if index == 0
                string = "#{string}, #{item[@config.labelKey]}" if index != 0
            return string
    methods:
        short: (text) ->
            return text if @config.max == null || @config.max == undefined
            return text if text.length < @config.max
            return "#{text.slice(-5)}..."

</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
</style>