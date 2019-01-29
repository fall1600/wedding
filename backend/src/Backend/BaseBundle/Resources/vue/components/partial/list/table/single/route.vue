<template>
    <div>
        <router-link :to="route" :target="target" data-role="list-route">
            {{ data }}
        </router-link>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
queryString = require 'qs'
module.exports =
    props: ['position', 'dataType'],
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
            return {} if @config instanceof Object == false
            return {} if @config.route instanceof Object == false
            _route =
              name: ''
              params: {}
              query: {}
            _route.name = @config.route.name
            if @config.route.params instanceof Array
              for param in @config.route.params
                  _route.params[param.name] = @routeReferenceData param.reference
            if @config.route.query instanceof Object
              _route.query = @config.route.query
            return _route
        target: () ->
            return '_self' if @config == null
            return '_self' if @config.target == null || @config.target == undefined
            return @config.target
    methods:
        routeReferenceData: (keys) ->
            data = @singleData
            for key in keys.split('.')
                return 'null' if data[key] == undefined || data[key] == null || data[key] == ''
                data = data[key]
            return data
</script>

<style lang="sass?indentedSyntax" type="text/sass">
a[data-role="list-route"]
    color: #337ab7
    &:hover, &:visited, &:active
        color: #337ab7
        text-decoration: underline
</style>