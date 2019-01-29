<template>
    <div class="list-container">
        <table class="table table-hover table-striped jambo_table">
            <thead>
                <tr>
                    <th class="checkbox-colum" v-if="batchConfig.length > 0"></th>
                    <th v-for="(text, dataKey) in listConfig.labels" v-on:click="changeOrder(dataKey)" v-if="listConfig.type[dataKey] != 'hidden'">
                        {{ text|trans({}, 'forms') }}
                        <i class="fa" :class="{'fa-unsorted': typeof filterData.sort[dataKey] == 'undefined', 'fa-sort-asc': filterData.sort[dataKey] == 'asc', 'fa-sort-desc': filterData.sort[dataKey] == 'desc'}" v-if="enableSort(dataKey)"></i>
                    </th>

                    <th v-if="actionButton.length > 0">
                        {{'index.action'|trans}}
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="dataRow.length == 0">
                    <th :colspan="columLength" class="dataNullPrompt">
                        {{'message.data.empty'|trans}}
                    </th>
                </tr>
                <list-table-single  v-if="dataRow.length != 0" v-for="(listItem, index) in dataRow" :rowIndex="index" :batch-config="batchConfig"></list-table-single>
            </tbody>
        </table>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ["batchConfig"]
    components:
        "list-table-single": require "components/backendbase/partial/list/table/single.vue"
    mounted: () ->
        @columLength = $('.list-container').find('table thead').find('th').length
    data: () ->
        columLength: 0
    methods:
        # 依照head點選的欄位排序
        changeOrder: (orderKey) ->
            for sort in @listConfig.sort
                if sort == orderKey
                    @$store.dispatch 'list.updateOrder', orderKey

        # 是否允許排序
        enableSort: (dataKey) ->
            for sort in @listConfig.sort
                if dataKey == sort
                    return true
            return false
    computed:
        listConfig: () ->
            return @$store.getters.listConfig
        dataRow: () ->
            return @$store.getters.listData
        filterData: () ->
            return @$store.getters.filterData
        actionButton: () ->
            return @$store.getters.actionButton
    watch:
        dataRow:
            deep: true
            handler: () ->
                @columLength = $('.list-container').find('table thead').find('th').length
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import '../../../assets/sass/color'
    .list-container
    th
        cursor: pointer
        min-width: 80px
        overflow: auto
    thead
        &>tr
            .checkbox-colum
                max-width: 20px

    .dataNullPrompt
        text-align: center
    .table
        margin-bottom: 0
</style>