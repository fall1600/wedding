<template>
    <div class="form-group" :class="setGrid(config.grid)">
        <label class="control-label col-md-2 col-sm-2 col-xs-12">
            {{ config.text|trans }}
        </label>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <table class="table">
                <thead>
                    <tr>
                        <th v-for="title in config.table.title">
                            {{ title|trans }}
                        </th>
                    </tr>
                </thead>
                <tbody v-if="renderData.length == 0">
                    <tr>
                        <td :colspan="config.table.data.length">
                            {{ config.table.null_message| trans }}
                        </td>
                    </tr>
                </tbody>

                <tbody v-if="renderData.length != 0">
                    <tr v-for="data in renderData">
                        <td v-for="(td, index) in config.table.data" v-if="hasComponent(td.type)">
                            <components v-bind:is="'td-'+td.type" :data="data[td.name]" :config="{index: index, key: td.name, array: config.name, colum: td}"></components>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['config']
    mixins: [require "components/backendbase/mixins/viewComponent.coffee"]
    computed:
        viewData: () ->
            return @$store.getters.view.data
        renderData: () ->
            data = @viewData
            for node in @config.name.split('.')
                if data[node] != undefined && data[node] != null
                    data = data[node]
                else
                    data = ''
            return data
    methods:
        trans: (label) ->
            if @config.label_prefix != undefined
                label = "#{@config.label_prefix}.#{label}"
            @$options.filters.trans label
        hasComponent: (type) ->
            @$options.components["td-#{type}"] != undefined
    components:
        'td-string': require 'components/backendbase/partial/view/table/string.vue'
        'td-datetime': require 'components/backendbase/partial/view/table/datetime.vue'
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
.form-group
    border-color: 1px #ccc solid
</style>