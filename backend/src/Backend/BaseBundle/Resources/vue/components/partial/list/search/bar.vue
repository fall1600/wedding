<template>
    <div class="search-bar">
        <!--text, number-->
        <div class="form-bar__text">
            <label class="form-bar__label">
                {{ listConfig.labels[search.key]|trans }}
            </label>
            <div class="form-bar__input">
                <component v-bind:is="hasComponent(listConfig.type[search.key], listConfig.searchConfig[search.key])"
                   :search="search" :index="index" :searchConfig="listConfig.searchConfig[search.key]"></component>
            </div>

            <div class="form-bar__btn">
                <button class="btn btn-danger" v-on:click="removeSearchBar">
                    <i class="fa fa-close"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        mounted: () ->
            @$root.$on 'setSearchData', @setSearchData
        props: ['search', 'index']
        data: () ->
            searchInfo: ''
            statusCheck: true
        computed:
            listConfig: () ->
                return @$store.getters.listConfig
        methods:
            setSearchData: () ->
                # 空白的直接移除
                if $(@$el).find('.status-search').length != 0
                    @searchInfo = @statusCheck

                if @search.used == true && @searchInfo != '' && $(@$el).is(":visible")
                    @$store.dispatch 'list.setSearch',
                        key: @search.key
                        info: @searchInfo
            removeSearchBar: () ->
                @$store.dispatch 'list.syncSearchStatus',
                    index: @index
                    key: @search.key
                    used: false
            startSearch: () ->
                @$root.$emit 'setSearchData'
            hasComponent: (searchType, searchConfig) ->
                if searchConfig == undefined
                    if searchType == 'text' || searchType == 'number'
                        return 'search-text'
                    else if searchType == 'date' || searchType == 'datetime-local'
                        return 'search-date'
                    else if searchType == 'checkbox'
                        return 'search-radio'
                else
                    return "search-#{searchConfig.type}"
        components:
            'search-text': require 'components/backendbase/partial/list/search/mode/text.vue'
            'search-date': require 'components/backendbase/partial/list/search/mode/date.vue'
            'search-radio': require 'components/backendbase/partial/list/search/mode/radio.vue'
            'search-select': require 'components/backendbase/partial/list/search/mode/select.vue'
            'search-boolean': require 'components/backendbase/partial/list/search/mode/boolean.vue'
            'search-daterange': require 'components/backendbase/partial/list/search/mode/daterange.vue'
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .search-bar
        +inline(2, .5em)
        +min-screen(1200px)
            +inline(3, .5em)
        +max-screen(480px)
            +inline(1, .2em)
    .form-bar
        &__text
            display: table
            width: 100%
            position: relative
            padding: 0
            > *
                display: table-cell
                vertical-align: middle
        &__btn
            width: 1%
            button
                margin: 0
        &__label
            width: 20%
            text-align: right
            font-weight: normal
            font-size: 1.1em
        &__input, &__btn, &__radio
            padding-left: .5em
        &__radio
            @extend %form-radio
</style>