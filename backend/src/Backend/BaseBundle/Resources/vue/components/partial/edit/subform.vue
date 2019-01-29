<template>
    <div class="subform" :class="setGrid(toolConfig.grid)">
        <!--{{ toolConfig }}-->
        <!--<br><br>-->

        <div role="tabpanel">
            <ul class="nav nav-tabs bar_tabs" role="tablist">
                <li role="presentation" v-for="(tab, index) in toolConfig.config" :class="{ active: activeTab(index) }">
                    <a role="tab" data-toggle="tab" :href="'#'+tab.name">
                        {{ tab.text|trans }}
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in" v-for="(tab, index) in toolConfig.config" :id="tab.name" :class="{ active: activeTab(index) }">
                    <div v-for="component in tab.content">
                        <div v-if="hasEditComponent(component.type)">
                            <component v-bind:is="'edit-'+component.type" :toolConfig="reFormatItem(toolConfig.config[index].name, component)"></component>
                        </div>
                    </div>
                </div>
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
        editSetting: () ->
            return @$store.getters.editSetting
    methods:
        reFormatItem: (parent, component) ->
            node = "#{parent}.#{component.name}"
            newFormat = {}
            for key of component
                if key == 'name'
                    newFormat.name = node
                else
                    newFormat[key] = component[key]
            newFormat.deep = true
            return newFormat

        hasEditComponent: (name) ->
            @$options.components["edit-#{name}"] != undefined
        activeTab: (index) ->
            return true if index == 0
            return false
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
.subform
    margin: 50px 0 50px 0
</style>
