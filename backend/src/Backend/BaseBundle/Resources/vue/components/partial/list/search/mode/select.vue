<template>
    <div>
        <select class="form-control" v-if="choices.length != 0" v-model="searchInfo">
            <option :value="choice[searchConfig.choiceValue]" v-for="choice in choices" v-if="choice[searchConfig.choiceValue] != null && choice[searchConfig.choiceValue] != undefined">
                {{ choice[searchConfig.choiceLabel]| trans }}
            </option>
        </select>

        <span class="warning-msg text-danger" v-if="choices.length == 0 && loaded == true">
            <i class="fa fa-warning"></i>
            {{ 'search.choice.warning.noChoices'|trans }}
        </span>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['search', 'searchConfig']
    mounted: () ->
        @getApi()
        @$root.$on 'setSearchData', @setSearchData
    data: () ->
        searchInfo: ''
        choices: []
        loaded: false
    beforeDestroy: () ->
        @$root.$off 'setSearchData', @setSearchData
    methods:
        init: () ->
            if @filterData.search[@searchConfig.key] != undefined
                @searchInfo = @filterData.search[@searchConfig.key]
        getApi: () ->
            if @searchConfig.options != undefined
                @generateOptions @searchConfig.options
                return

            me = @
            apiName = @api
            return if @searchConfig.api == undefined
            for apiNode in @searchConfig.api.split('.')
                apiName = apiName[apiNode]

            apiName()
            .then (result) ->
                me.generateOptions result
        generateOptions: (result) ->
            @choices = result
            @choices = [] if @choices == undefined || @choices == null
            @loaded = true
            return if @choices[0] == undefined
            @searchInfo = @choices[0][@searchConfig.choiceValue]
            @init()
        setSearchData: () ->
            if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                @$store.dispatch 'list.setSearch',
                    key: @searchConfig.key
                    info: @searchInfo
        startSearch: () ->
            @$root.$emit 'setSearchData'
    computed:
        api: () ->
            return @$store.state.base.api
        filterData: () ->
            return @$store.getters.filterData

</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
span.warning-msg
    margin-left: 30px
</style>