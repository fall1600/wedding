<template>
    <div>
        <router-link :title="data" :to="route" :target="target">
            {{ short(data) }}
        </router-link>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
queryString = require 'qs'
module.exports =
    props: ['position', 'dataType'],
    mounted: () ->
        @$root.$on 'quick-edit-list-item',  @editable
    computed:
        singleData: () -> @$store.getters.listData[@position.row]
        listConfig: () -> @$store.getters.listConfig
        config: () -> @listConfig.config[@position.key]
        data: () ->
            data = @singleData
            keys = @position.key.split('.')
            for key in keys
                return '' if data[key] == undefined || data[key] == null
                data = data[key]
            return data
        route: () ->
            return {} if @config == null
            return {} if @config.route == null || @config.route == undefined

            reference = @config.reference
            route = $.extend({}, @config.route)
            return route if reference == null || reference == undefined

            # 指定filter參考的欄位
            filter = route.query
            filter = {} if filter == null || filter == undefined
            for item in reference
                filter[item.type] = {} if filter[item.type] == undefined
                filter[item.type][item.filterBy] = @routeReferenceData item.key

            result = $.extend({}, filter)
            result.search = queryString.stringify result.search if queryString.stringify(result.search).trim() != ''
            result.sort = queryString.stringify result.sort if queryString.stringify(result.sort).trim() != ''
            route.query = result
            return route
        target: () ->
            return '_self' if @config == null
            return '_self' if @config.target == null || @config.target == undefined
            return @config.target
    methods:
        routeReferenceData: (keys) ->
            data = @singleData
            keys = @position.key.split('.')
            for key in keys
                return '' if data[key] == undefined || data[key] == null
                data = data[key]
            return data
        short: (text) ->
            return text if @config.max == null || @config.max == undefined
            return text if text.length < @config.max
            return "#{text.slice(-5)}..."
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
</style>