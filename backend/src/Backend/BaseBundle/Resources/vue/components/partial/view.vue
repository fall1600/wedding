<template>
    <div class="view-container">
        <!--{{ viewData }}-->
        <!--<br><br>-->
        <view-tools :backRoute="backRoute"></view-tools>
        <template v-if="!loading">
            <div class="x_panel">
                <div class="x_content">
                    <div data-parsley-validate class="form-horizontal form-label-left">
                        <component v-if="useCustomize" v-bind:is="component" v-for="component in useCustomize"></component>

                        <div :class="setGrid(grid)">
                            <h2 v-if="useSubTitle(subTitle)">{{ subTitle|trans }}</h2>
                            <hr v-if="useSubTitle(subTitle)">
                            <template v-for="(config, index) in viewConfig">
                                <div class="col-xs-12" v-if="config.subTitle != undefined">
                                    <h2 class="subTitle">
                                        {{ config.subTitle|trans }}
                                    </h2>
                                    <hr>
                                </div>

                                <template class="edit-tools form-horizontal form-label-left" v-if="hasEditComponent(config.type)">
                                    <component v-bind:is="'view-'+config.type" :config="config"></component>
                                </template>

                                <div v-if="config.type == 'customize'">
                                    <component v-bind:is="config.component"></component>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        props: ['useCustomize', 'grid', 'subTitle', 'backRoute']
        mixins: [require "components/backendbase/mixins/view.coffee"]
        computed:
            viewData: () ->
                return @$store.getters.view.data
            loading: () ->
                return @$store.getters.loading
            viewConfig: () ->
                return @$store.getters.view.config
        methods:
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
                @$options.components["view-#{name}"] != undefined
</script>

<style lang="sass?indentedSyntax" type="text/sass">
.view-container
    h2
        margin-top: 50px

.subTitle
    margin: 20px 0 20px 0
</style>
