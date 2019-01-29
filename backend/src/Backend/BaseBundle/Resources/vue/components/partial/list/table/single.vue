<template>
    <tr>
        <td class="form-checkbox" v-if="batchConfig.length > 0">
            <input :id="rowIndex" type="checkbox" v-model="selectStatus">
            <label :for="rowIndex"></label>
        </td>

        <td v-for="(dataType, dataKey) in listConfig.type" v-if="dataType != 'hidden'">
            <list-table-single-editable v-if="!hasListComponent(dataType) && dataType != 'customize'"
                          :position="{'row': rowIndex, 'key': dataKey}" :dataEditable="dataEditable(dataKey)"
                          :dataType="dataType">
            </list-table-single-editable>

            <component v-if="dataType == 'customize'"
                :is="listConfig.config[dataKey].component" :dataId="singleData.id" :index="rowIndex">
            </component>

            <component v-if="hasListComponent(dataType) && dataType != 'customize'" :is="'list-table-single-'+dataType"
                :column="singleData[dataKey]" :position="{'row': rowIndex, 'key': dataKey }"
                ></component>
        </td>

        <td v-if="actionButton.length > 0">
            <span v-for="acitonColum in listConfig.action">
                <span v-if="typeof(acitonColum) == 'object' && (typeof(acitonColum.roles) == 'undefined' || hasRole(acitonColum.roles))">
                    <router-link v-if="typeof(acitonColum.route) != 'undefined'" class="btn btn-success" :to="{name: acitonColum.route, params:{id: singleData.id}}">
                        {{acitonColum.label|trans}}
                    </router-link>
                    <!--載入客製元件-->
                    <component v-if="typeof(acitonColum) == 'object' && typeof(acitonColum.component) != 'undefined'"
                       :is="acitonColum.component" :dataId="singleData.id" :index="rowIndex" :action-column="acitonColum"></component>
                </span>
            </span>
        </td>
    </tr>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mounted: () ->
        # 收到全選的廣播事件
        me = @
        @$root.$on 'sync-select-all-status', (newStatus) ->
            me.selectStatus = newStatus
    mixins:  [
        require "components/backendbase/mixins/base.coffee"
        require "components/backendbase/mixins/token.coffee"
    ]
    props: ['listItem', 'rowIndex', 'batchConfig']
    computed:
        baseRouteName: () ->
            "#{@$route.name.split('-')[0]}-"
        checkStatus: () ->
            return @$store.getters.checkStatus
        listConfig: () ->
            return @$store.getters.listConfig
        singleData: () ->
            return @$store.getters.listData[@rowIndex]
        detailPath: () ->
            path = "#{@$route.name.split('-')[0]}-detail"
            return path
        editPath: () ->
            path = "#{@$route.name.split('-')[0]}-edit"
            return path
        actionButton: () ->
            return @$store.getters.actionButton
    methods:
        # 依照資料設定允許快速編輯
        dataEditable: (dataKey) ->
            for quickEditData in @listConfig.quickEdit
                if quickEditData == dataKey
                    return true
            return false
        hasListComponent: (name) ->
            @$options.components["list-table-single-#{name}"] != undefined
    data: () ->
        selectStatus: false
    watch:
        selectStatus: () ->
            me = @
            me.$store.dispatch 'list.syncCheckStatus',
                row: me.rowIndex
                status: me.selectStatus

        checkStatus: () ->
            @selectStatus = @checkStatus[@rowIndex].status
    components:
        "list-table-single-checkbox": require "components/backendbase/partial/list/table/single/switch.vue"
        "list-table-single-datepicker": require "components/backendbase/partial/list/table/single/datepicker.vue"
        "list-table-single-relational": require "components/backendbase/partial/list/table/single/relational.vue"
        "list-table-single-editable": require "components/backendbase/partial/list/table/single/editable.vue"
        "list-table-single-image": require "components/backendbase/partial/list/table/single/image.vue"
        "list-table-single-id": require "components/backendbase/partial/list/table/single/id.vue"
        "list-table-single-link": require "components/backendbase/partial/list/table/single/link.vue"
        "list-table-single-route": require "components/backendbase/partial/list/table/single/route.vue"
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import '../assets/sass/base.sass'
    .form-checkbox
        @extend %form-checkbox
    td
        min-height: 50px
        &:hover
            cursor: pointer
        &.form-checkbox
            max-width: 20px
            label
                margin-top: 0
                max-width: none

    /*設定排序欄位寬度*/
    td:nth-last-child(2)
        width: 110px

    /*設定欄位寬度*/
    td:nth-last-child(1)
        width: 400px

    /*第一個向上排序鈕隱藏*/
    tr:nth-child(1)
        .control-order
            .btn:nth-child(1)
                display: none

    /*最後一個向下排序鈕隱藏*/
    tr:nth-last-child(1)
        .control-order
            .btn:nth-child(2)
                display: none

    /*排序鈕的style*/
    .control-order
        .btn
            font-size: 18px
</style>