<template>
    <div class="search-container">
        <div class="search-condition" v-if="listConfig.search.length != 0">
            <span class="search-condition__title">
                {{'form.button.search'|trans}} <i class="fa fa-caret-right" aria-hidden="true"></i>
            </span>
            <ul class="search-condition__list">
                <li v-for="(search, index) in searchStatus">
                    <a>
                        <list-search-condition :searchKey="search.key" :index="index"></list-search-condition>
                    </a>
                </li>
            </ul>
            <transition name="fade">
                <button class="form-btn btn btn-success pull-right" v-on:click="startSearch" v-if="showSearchBtn">
                    <i class="fa fa-search"></i>
                    <span>
                        {{'form.button.search'|trans}}
                    </span>
                </button>
            </transition>
        </div>
        <transition name="fade">
            <div class="search-zone search-condition__content" v-if="showSearchBtn">
                <div class="form">
                    <transition-group name="fade">
                        <list-search-bar v-for="(search, index) in searchStatus" v-if="search.used" :key="index" :search="search" :index="index"></list-search-bar>
                    </transition-group>
                </div>
            </div>
        </transition>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
# <script lang="coffee" type="text/coffeescript">
module.exports =
    mounted: () ->
        $('.dropdown-menu').click (event) ->
            event.stopPropagation()
        @setSearchBar(true)
    data: () ->
        showSearchBtn: false
    components:
        'list-search-condition': require 'components/backendbase/partial/list/search/condition.vue'
        'list-search-bar': require 'components/backendbase/partial/list/search/bar.vue'
    computed:
        searchStatus: () ->
            return @$store.getters.searchStatus
        listConfig: () ->
            return @$store.getters.listConfig
    methods:
        startSearch: () ->
            @$root.$emit 'setSearchData'
        clearSearch: () ->
            @$store.dispatch 'list.search.clear'
        setSearchBar: (isMounted) ->
            me = @
            for search in @searchStatus
                if search.used == true
                    me.showSearchBtn = true
                    return
            @clearSearch() if isMounted != true
            me.showSearchBtn = false
    watch:
        searchStatus:
            deep: true
            handler: () ->
                @setSearchBar()
</script>


<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .search-container
        clear: both
        .search-zone // = search-condition__content
            @extend %clearfix
            background: white
            padding: 1em
            border: 1px solid lighten(black, 90%)
    .search-condition
        @extend %clearfix
        background: lighten(black, 92.5%)
        padding: 1em
        line-height: 1.5em
        font-size: 1em
        .form
            &-btn
                float: right
                margin: 0   
        &__title
            font-size: 1.2em
            color: color_main('theme')
            .fa
                font-size: .8em
        &__list
            display: inline-block
            margin: 0
            padding: 0
            list-style-type: none
            > li
                display: inline-block
                margin-left: 1em


    .fade-enter-active, .fade-leave-active
        transition: opacity .5s
    .fade-enter, .fade-leave-active
        opacity: 0
</style>