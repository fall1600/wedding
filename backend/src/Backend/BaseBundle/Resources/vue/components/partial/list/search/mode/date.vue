<template>
    <input type="date" class="form-control" v-model="searchInfo" @keyup.enter="startSearch">
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['search']
    mounted: () ->
        @$root.$on 'setSearchData', @setSearchData
    data: () ->
        searchInfo: ''
    beforeDestroy: () ->
        @$root.$off 'setSearchData', @setSearchData
    methods:
        setSearchData: () ->
            if $(@$el).find('.status-search').length != 0
                @searchInfo = @statusCheck

            if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                @$store.dispatch 'list.setSearch',
                    key: @search.key
                    info:
                        max: "#{@searchInfo} 23:59:59"
                        min: "#{@searchInfo} 00:00:00"
        startSearch: () ->
            @$root.$emit 'setSearchData'
</script>