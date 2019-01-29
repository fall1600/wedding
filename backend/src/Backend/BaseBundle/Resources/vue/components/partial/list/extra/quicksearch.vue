<template>
    <div class="quick-seach-container" v-if="startRender">
        <div class="filter">
            <a v-for="filter in quickSearchData" class="btn btn-app btn-sm" @click="startSearch(filter.value)" :class="{ active: activeButton(filter.value) }">
                <span class="badge bg-green">
                    {{ filter.amount }}
                </span>

                <span v-if="$route.name == 'banner-list'">
                    {{ filter.title| trans }}
                </span>

                <span v-if="$route.name != 'banner-list'">
                    {{ filter.label| trans }}
                </span>
            </a>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mounted: () ->
        @init()
        @$root.$on 'list.switch.success', @getQuickSearchData
    beforeDestroy: () ->
        @$root.$off 'list.switch.success'
    data: () ->
        startRender: false
        quickSearchData: []
        apiName: ''
        key: ''
    computed:
        search: () ->
            return @$store.getters.filterData.search
        extra: () ->
            return @$store.getters.listConfig.extra
        api: () ->
            return @$store.state.base.api
    methods:
        init: () ->
            @startRender = false
            @getQuickSearchData()
        activeButton: (filerData) ->
            filterAmount = 0
            for filter of @search
                filterAmount++
                if filter == @key && @search[filter] == filerData.toString()
                    return true
            if filerData == 'all' && filterAmount == 0
                return true
            return false
        getQuickSearchData: () ->
            me = @
            getStatusApi = @api
            extraTools = @$store.getters.listConfig.extra

            new Promise (resolve, reject) ->
                for extra in extraTools
                    do (extra) ->
                        if extra.name == 'quick_search'
                            me.key = extra.config.key
                            me.apiName = extra.config.apiName

                            # 不固定狀態, 需要先發一次request取得狀態
                            if extra.config.getStatus != undefined
                                for apiNode in extra.config.getStatus.split('.')
                                    getStatusApi = getStatusApi[apiNode]
                                getStatusApi().then (result) ->
                                    for status in result
                                        status.label = status.name
                                        status.value = status.id
                                    me.$set me, 'quickSearchData', extra.config.status.concat result
                                    resolve()
                            else
                                me.$set me, 'quickSearchData', extra.config.status
                                resolve()
            .then () ->
                # 取得數量
                promiseList = []
                for filter, index in me.quickSearchData
                    searchData =
                        page: 1
                        search: {}
                        sort: {}
                    if filter.value != 'all'
                        searchData.search[me.key] = filter.value
                    do (index) ->
                        promise = me.api[me.apiName].search searchData
                        promiseList.push promise
                        promise.then (result) ->
                              me.$set me.quickSearchData[index], 'amount', result.pager.rows


                              Promise.all(promiseList).then () ->
                    me.startRender = true

        startSearch: (filterData) ->
            @$store.dispatch 'list.setSearch',
                key: @key
                info: filterData
</script>


<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .quick-seach-container
        display: inline-block
        margin: 0 .5em
        .filter
            display: inline-block


            .btn-app
                height: auto
                margin: 0 .5em
                padding: .5em
                max-height: 34px
                line-height: 1.8
                &.active
                    border-color: color_main('theme')
                    color: color_main('theme')
</style>