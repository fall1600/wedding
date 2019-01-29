<template>
    <div class="form-bar__radio">
        <input type="radio" id="status-true" name="status" v-model="statusCheck" :value="trueValue">
        <label for="status-true">{{ trueLabel() }}</label>
        <input type="radio" id="status-false" name="status" v-model="statusCheck" :value="falseValue">
        <label for="status-false">{{ falseLabel() }}</label>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['search', 'index', 'searchConfig']
    mounted: () ->
        @$root.$on 'setSearchData', @setSearchData
        @init()
    data: () ->
        statusCheck: true
    beforeDestroy: () ->
        @$root.$off 'setSearchData', @setSearchData
    computed:
        filterData: () ->
            return @$store.getters.filterData
        defaultTrueLabel: () -> @$options.filters.trans 'index.search.boolean.default_label.true'
        defaultFalseLabel: () -> @$options.filters.trans 'index.search.boolean.default_label.false'
        trueValue: () ->
            return 'true' if @searchConfig == null || @searchConfig == undefined
            return 'true' if @searchConfig.value == null || @searchConfig.value == undefined
            return 'true' if @searchConfig.value.true == null || @searchConfig.value.true == undefined
            return @searchConfig.value.true
        falseValue: () ->
            return 'false' if @searchConfig == null || @searchConfig == undefined
            return 'false' if @searchConfig.value == null || @searchConfig.value == undefined
            return 'false' if @searchConfig.value.false == null || @searchConfig.value.false == undefined
            return @searchConfig.value.false
    methods:
        init: () ->
            @statusCheck = @filterData.search[@search.key]
#            if @statusCheck == undefined
#                @removeSearchBar()
        setSearchData: () ->
            if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                @$store.dispatch 'list.setSearch',
                    key: @search.key
                    info: @statusCheck
        startSearch: () ->
            @$root.$emit 'setSearchData'
        removeSearchBar: () ->
            @$store.dispatch 'list.syncSearchStatus',
                index: @index
                key: @search.key
                used: false
        trueLabel: () ->
            return @defaultTrueLabel if @searchConfig == null || @searchConfig == undefined
            return @defaultTrueLabel if @searchConfig.label == null || @searchConfig.label == undefined
            return @defaultTrueLabel if @searchConfig.label.true == null || @searchConfig.label.true == undefined
            return @$options.filters.trans @searchConfig.label.true
        falseLabel: () ->
            return @defaultFalseLabel if @searchConfig == null || @searchConfig == undefined
            return @defaultFalseLabel if @searchConfig.label == null || @searchConfig.label == undefined
            return @defaultFalseLabel if @searchConfig.label.false == null || @searchConfig.label.false == undefined
            return @$options.filters.trans @searchConfig.label.false
    watch:
        filterData:
            deep: true
            handler: () ->
                @init()
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
@import "../assets/sass/base.sass"
.form-bar__radio
    @extend %form-radio
</style>