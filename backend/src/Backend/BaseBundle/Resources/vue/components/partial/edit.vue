<template>
    <div class="edit-container">
        <!--{{ editModel.editSetting }}-->
        <!--<br><br>-->
        <!--{{ editSetting.dataRow }}-->
        <template v-if="!loading">
            <edit-manage :customizeConfig="customizeConfig" :position="'top'"></edit-manage>

            <div class="x_panel">
                <div class="x_content">
                    <div data-parsley-validate class="form-horizontal form-label-left">
                        <component class="order__customize" v-if="useCustomize" v-bind:is="component" v-for="component in useCustomize"></component>
                        <div class="order__state" :class="setGrid(grid)">
                            <h2 v-if="useSubTitle(subTitle)">{{ subTitle|trans }}</h2>
                            <hr v-if="useSubTitle(subTitle)">
                            <div class="edit-tools form-horizontal form-label-left" v-for="(tool, index) in editSetting.config" data-parsley-validate>
                                <h2 v-if="tool.sub_title != undefined" class="sub_title">
                                    {{tool.sub_title|trans}}
                                </h2>
                                <div v-if="hasEditComponent(tool.type)">
                                    <component v-bind:is="'edit-'+tool.type" :toolConfig="tool"></component>
                                </div>

                                <div v-if="tool.type == 'customize' && dynamicCreated(tool.dynamicCreated, tool.data)">
                                    <component v-bind:is="tool.component"></component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--<edit-manage></edit-manage>-->
        </template>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
# <script lang="coffee" type="text/coffeescript">
    module.exports =
        props: ['useCustomize', 'grid', 'subTitle', 'customizeConfig']

        # 載入表單元件
        mixins:  [require "components/backendbase/mixins/form.coffee"]
        computed:
            editSetting: () ->
                return @$store.getters.editSetting
            loading: () ->
                return @$store.getters.loading
            editModel: () ->
                return @$store.getters.editModel
        methods:
            dynamicCreated: (isDynamicCreated, isCreated) ->
                if isDynamicCreated != undefined
                    if isDynamicCreated == true && isCreated == true
                        return true
                    else
                        return false
                else
                    return true
            useSubTitle: (subTitle) ->
                if subTitle != undefined
                    return true
                else
                    return false
            setGrid: (grid) ->
                if grid == 'two'
                    return 'col-sm-6'
                else
                    return 'col-xs-10'
            hasEditComponent: (name) ->
                @$options.components["edit-#{name}"] != undefined
        components:
            'edit-subform': require "components/backendbase/partial/edit/subform.vue"
            'edit-repeatedform': require "components/backendbase/partial/edit/repeatedform.vue"
</script>

<style lang="sass?indentedSyntax" type="text/sass">
.control-label.required::before
    content: '*'
    padding-right: 3px
    font-weight: bolder
    color: red
    display: inline-block
.form-control:focus
    border-color: #1ab394 !important
.select2.select2-container.select2-container--default
    width: 100% !important
.order
    &__customize, &__state
        margin-bottom: 1em
.sub_title
    margin: 50px 0
    padding: 5px
    border-bottom: 1px solid #ccc
</style>
