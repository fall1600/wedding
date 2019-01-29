<template>
    <div class="form-group" v-if="rerenderRepeatedForm" :class="setGrid(toolConfig.grid)">
        <!--{{ toolConfig }}-->
        <!--<br><br>-->

        <label class="control-label col-md-4 col-sm-4 col-xs-12">
            {{ toolConfig.text |trans }}
        </label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <button class="btn btn-default btn-sm" v-if="toolConfig.allowAdd" @click="createRepeateForm()">
                <i class="fa fa-plus"></i>
                <span>
                    新增項目
                </span>
            </button>

            <div v-for="(data, dataIndex) in editSetting.dataRow[toolConfig.name]" class="item">
                <button class="btn btn-danger btn-xs delete-btn" v-if="toolConfig.allowAdd" @click="deleteItem(dataIndex)">
                    <i class="fa fa-close"></i>
                </button>
                <div v-for="(component, index) in toolConfig.config" v-if="hasEditComponent(component.type)">
                    <component v-bind:is="'edit-'+component.type" :toolConfig="reFormatItem(toolConfig.name, component, dataIndex)"></component>
                    <br><br><br>
                </div>
                <hr>
            </div>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    mixins:  [
        require "components/backendbase/mixins/form.coffee"
        require "components/backendbase/mixins/formComponent.coffee"
    ]
    props: ['toolConfig', 'data']
    computed:
        rerenderRepeatedForm: () ->
            return @$store.getters.rerenderRepeatedForm
        editSetting : () ->
            # 如果無資料, 新增一筆空資料
            if @$store.getters.editSetting.dataRow[@toolConfig.name] == undefined
                @createRepeateForm('new')
            else
                if @$store.getters.editSetting.dataRow[@toolConfig.name].length == 0
                    @createRepeateForm('new')
            return @$store.getters.editSetting
    methods:
        repeateTime: (name) ->
            return @editSetting.dataRow[name].length
        reFormatItem: (parent, component, dataIndex) ->
            node = "#{parent}.#{component.name}"
            newFormat = {}
            for key of component
                if key == 'name'
                    newFormat.name = component.name
                else
                    newFormat[key] = component[key]
            newFormat.repeated = true
            newFormat.parent = parent
            newFormat.dataIndex = dataIndex
            return newFormat
        hasEditComponent: (name) ->
            @$options.components["edit-#{name}"] != undefined
        activeTab: (index) ->
            return true if index == 0
            return false
        createRepeateForm: (mode) ->
            @$store.dispatch 'edit.create.repeatedForm',
                mode: mode
                parent: @toolConfig.name
                configs: @toolConfig.config
        deleteItem: (index) ->
            @$store.dispatch 'edit.delete.repeatedForm',
                index: index
                parent: @toolConfig.name
                configs: @toolConfig.config
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .item
        position: relative
        .delete-btn
            position: absolute
            right: 0
            top: 0
</style>
