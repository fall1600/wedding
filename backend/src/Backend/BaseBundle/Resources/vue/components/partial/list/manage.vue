<template>
    <div class="list-manage-container" :class="position">
        <div class="edit-list" v-if="batchConfig.length != 0">
            <div class="form-checkbox selectAll">
                <input :id="'selectAllCheckbox' + position" type="checkbox" v-model="selectAllStatus">
                <label :for="'selectAllCheckbox' + position">{{'batch.select.all'|trans}}</label>
            </div>
            <div class="action btn-group" id="dropdown">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{'batch.button.action'|trans}}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li v-for="action in batchConfig">
                        <a v-if="!isComponent(action)" v-on:click="startAction(action.colum, action.action, action.value)">{{ ("batch." + action.name)|trans }}</a>
                        <component v-if="isComponent(action)" :is="action.component" :action="action" :checked-ids="checkedIds"></component>
                    </li>
                </ul>
            </div>
        </div>
       <div class="other-tools" v-if="position == 'top'">
           <span v-for="extraColumn in listConfig.extra" class="extra-component">
               <router-link v-if="typeof(extraColumn) == 'string'" class="btn btn-info" :to="{ name: baseRouteName + extraColumn.route }">456
                   {{extraColumn|trans}}
               </router-link>
               <span v-if="typeof(extraColumn) == 'object' && typeof(extraColumn.roles) != 'undefined' && hasRole(extraColumn.roles)">
                   <router-link v-if="typeof(extraColumn.route) != 'undefined'" class="btn btn-info" :to="{ name: extraColumn.route }">
                       {{extraColumn.label|trans}}
                   </router-link>

                   <component v-if="typeof(extraColumn) == 'object' && typeof(extraColumn.component) != 'undefined'" :is="extraColumn.component"></component>
               </span>
           </span>
       </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['position', 'batchConfig']
    mixins:  [
        require "components/backendbase/mixins/base.coffee"
        require "components/backendbase/mixins/token.coffee"
    ]
    mounted: () ->
        me = @
        $(@$el).find('#file-upload').change (event) ->
            me.$store.dispatch 'list.import.data', event.target.files

    events:
        'sync-select-all-status': (newStatus) ->
            @selectAllStatus = newStatus
    computed:
        baseRouteName: () ->
            "{@$route.name.split('-')[0]}-"
        checkStatus: () ->
            return @$store.getters.checkStatus
        listConfig: () ->
            return @$store.getters.listConfig
        listData: () ->
            return @$store.getters.listData
        checkedIds: () ->
            ids = []
            for dataCheck, index in @checkStatus
                if dataCheck.status == true
                    ids.push @listData[index].id
            ids
    data: () ->
        selectAllStatus: false
        fileList: []
    methods:
        isComponent: (action) ->
            action.component != undefined
        insertDataPath: () ->
            path = "#{@$route.name.split('-')[0]}-new"
            return path
        startAction: (colum, action, updateValue) ->
            $('#dropdown').removeClass('open')
            ids = []
            value = []

            for dataCheck, index in @checkStatus
                # 是被勾選的資料, 存入ids鎮列
                if dataCheck.status == true
                    ids.push @listData[index].id
            # 發出批次管理事件(id: 資料id, action: 批次管理動作)
            @$store.dispatch 'list.manage.group',
                ids: ids
                value: updateValue
                colum: colum
                action: action
        exportData: () ->
            @$store.dispatch 'list.exportData'
    watch:
        selectAllStatus: () ->
            @$root.$emit 'sync-select-all-status', @selectAllStatus

        checkStatus: () ->
            for status in @checkStatus
                if status.status == false
                    @selectAllStatus = false
                    return
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .list-manage-container
        @extend %clearfix
        width: 100%
        background: lighten(black, 87.5%)
        padding: .5em
        .btn
            margin: 0
        &>.checkbox, &>.action
            display: inline-block
            margin: 0 10px 0 10px

    .import-container
        display: inline-block
        position: relative

    label
        font-weight: normal
        cursor: pointer

    input[type="file"]
        display: none

    .edit-list
        margin-right: 150px

    .edit-list, .other-tools
        display: inline-block
    .other-tools
        float: right
        > *
           float: right
        .extra-component
            margin: 0 10px


    .selectAll, .action
        display: inline-block
    .action
        margin-left: 20px
    .form
        &-checkbox
            @extend %form-checkbox
</style>