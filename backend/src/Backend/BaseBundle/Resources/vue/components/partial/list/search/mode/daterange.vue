<template>
    <div data-role="search-daterange">
        <div class="component-datetime-picker">
            <datetime-picker :type="'date'" v-model="min"></datetime-picker>
        </div>
        <div class="component-datetime-picker">
            <datetime-picker :type="'date'" v-model="max"></datetime-picker>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['search', 'searchConfig']
    mounted: () ->
        @$root.$on 'setSearchData', @setSearchData
        @init()
    beforeDestroy: () ->
        @$root.$off 'setSearchData', @setSearchData
    data: () ->
        searchInfo: ''
        max: null
        min: null
        delimiter: '.'
    computed:
        filterData: () ->
            return @$store.getters.filterData
        useLike: () ->
            if @searchConfig != undefined
                if @searchConfig.like == true
                    return true
                else
                    return false
            else
                return false
        searchKey: () ->
            return @searchConfig.key if @searchConfig.key != null && @searchConfig.key != undefined
            return @search.key
    methods:
        init: () ->
            filter = @filterData.search[@searchKey]
            return if filter == undefined || filter == 'undefined'
            if @filterData.search[@searchKey] != undefined
                filter = @filterData.search[@searchKey]
            @min = filter.split(@delimiter)[0]
            @max = filter.split(@delimiter)[1]
        setSearchData: () ->
            if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                @$store.dispatch 'list.setSearch',
                    key: @searchKey
                    info: @searchInfo
        startSearch: () ->
            return if @searchInfo == ''
            @$root.$emit 'setSearchData'
        setValue: () ->
            return if @min == null || @min == undefined
            return if @max == null || @max == undefined
            @searchInfo = "#{@min}#{@delimiter}#{@max}"
    components:
        'datetime-picker': require 'components/backendbase/plugins/datetimepicker'
    watch:
        max: () ->
            @setValue()
        min: () ->
            @setValue()
        $route: () ->
            @init()

</script>
<style lang="sass?indentedSyntax" type="text/sass">
div[data-role="search-daterange"]
    .component-datetime-picker
        margin-right: 10px
        display: inline-block
        vertical-align: bottom
        height: 34px
        div[data-role="datetime-picker-wrap"]
            width: 150px
</style>