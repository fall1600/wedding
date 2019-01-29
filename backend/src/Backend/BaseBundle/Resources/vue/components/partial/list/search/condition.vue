<template>
    <a class="form-checkbox">
        <input :id="searchKey" type="checkbox" :searchKey="searchKey" v-model="realCheckStatus"> 
        <label :for="searchKey">{{ listConfig.labels[searchKey]|trans }}</label>
    </a>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['searchKey', 'index']
    mounted: () ->
        @init()
    data: () ->
        realCheckStatus: false
    computed:
        listConfig: () ->
            return @$store.getters.listConfig
        searchData: () ->
            return @$store.getters.searchStatus[@index]
    methods:
        init: () ->
            @realCheckStatus = @searchData.used
    watch:
        realCheckStatus: () ->
            key = @searchKey
            if @listConfig.searchConfig != undefined
                if @listConfig.searchConfig[@searchKey] != undefined
                    if @listConfig.searchConfig[@searchKey].key != undefined
                        key = @listConfig.searchConfig[@searchKey].key
            @$store.dispatch 'list.syncSearchStatus',
                index: @index
                key: key
                used: @realCheckStatus
        searchData:
            deep: true
            handler: () ->
                @realCheckStatus = @searchData.used
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .form-checkbox
        @extend %form-checkbox
</style>