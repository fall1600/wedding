<template>
    <input type="text" class="form-control" v-model="searchInfo" @keyup.enter="startSearch">
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
                filter = @filterData.search[@searchKey].replace('%', '').replace('%', '')
            @searchInfo = filter
        setSearchData: () ->
            if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                searchData = @searchInfo
                searchData = "%#{@searchInfo}%"
                @$store.dispatch 'list.setSearch',
                    key: @searchKey
                    info: searchData
        startSearch: () ->
            return if @searchInfo == ''
            @$root.$emit 'setSearchData'
</script>