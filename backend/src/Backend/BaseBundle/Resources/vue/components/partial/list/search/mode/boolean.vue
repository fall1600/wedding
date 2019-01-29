<template>
    <div class="form-bar__radio">
        <input type="radio" id="status-true" name="status" v-model="statusCheck" :value="true">
        <label for="status-true">{{trueLabel|trans}}</label>
        <input type="radio" id="status-false" name="status" v-model="statusCheck" :value="false">
        <label for="status-false">{{falseLabel|trans}}</label>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['search', 'searchConfig']
    mounted: () ->
        @$root.$on 'setSearchData', @setSearchData
        @init()
    data: () ->
        statusCheck: true
    beforeDestroy: () ->
        @$root.$off 'setSearchData', @setSearchData
    computed:
        trueLabel: () ->
            "#{@searchConfig.label_prefix}.true"
        falseLabel: () ->
            "#{@searchConfig.label_prefix}.false"
        filterData: () ->
            return @$store.getters.filterData
    methods:
        init: () ->
            @statusCheck = @filterData.search[@search.key]
        setSearchData: () ->

            if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                @$store.dispatch 'list.setSearch',
                    key: @search.key
                    info: @statusCheck
        startSearch: () ->
            @$root.$emit 'setSearchData'
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
@import "../assets/sass/base.sass"
.form-bar__radio
    @extend %form-radio
</style>