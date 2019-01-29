<template>
    <div class="edit-manage-container hidden-print" :class="{ fixed: isFixed }">
        <button class="btn btn-info" v-on:click="saveEditAndRedirect" :disabled="dislabledStatus" v-if="!isReadonly && checkRouteExist()">
            <i class="fa fa-arrow-left"></i>
            <span>
                {{'form.button.submit_back'|trans}}
            </span>
        </button>

        <button class="btn btn-info" v-on:click="saveEdit" :disabled="dislabledStatus" v-if="!isReadonly && saveBtnEnabled">
            <i class="fa fa-save"></i>
            <span>
                {{'form.button.submit'|trans}}
            </span>
        </button>

        <button class="btn btn-info" v-on:click="cancelEdit" v-if="!isReadonly">
            <i class="fa fa-close"></i>
            <span>
                {{'form.button.cancel'|trans}}
            </span>
        </button>

        <router-link :to="{ name: listRouter($route.name) }" v-if="isReadonly">
            <button class="btn btn-info">
                <i class="fa fa-arrow-left"></i>
                <span>
                    {{'form.button.previous'|trans}}
                </span>
            </button>
        </router-link>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['position', 'customizeConfig']
    data: () ->
        isFixed: false
        saveBtnEnabled: true
    mounted: () ->
        @setFixed()
        if @dislabledStatus == true
            @$store.dispatch 'edit.submitButton.active'
        if @customizeConfig != undefined
            if @customizeConfig.disableSaveBtn == true
                @saveBtnEnabled = false
    computed:
        editSetting: () ->
            return @$store.getters.editSetting
        editHandler: () ->
            return @$store.getters.editHandler
        isReadonly: () ->
            readonlyAmont = 0
            for config in @editSetting.config
                if config.readonly == true
                    readonlyAmont++
            if readonlyAmont == @editSetting.config.length
                return true
            else
                return false
        dislabledStatus: () ->
            return @$store.getters.disableSubmitButton
    methods:
        checkRouteExist: () ->
            rules = @$root.$router.options.routes
            target = @$route.name.replace('edit', 'list')
            for rule in rules
                if target == rule.name
                    return true
            return false
        saveEdit: () ->
            @$root.$emit 'save-edit'
        saveEditAndRedirect: () ->
            @$root.$emit 'save-edit', 'list'
        cancelEdit: () ->
            @$router.go(-1)
        listRouter: (routeName) ->
            return routeName.replace('edit', 'list')
        setFixed: () ->
            if @position != 'top'
                @isFixed = false
                return
            me = @
            $(window).scroll () ->
                position = $('body').scrollTop()
                if position > 123
                    me.isFixed = true
                else
                    me.isFixed = false
    watch:
        editSetting:
            deep: true
            handler: () ->
                if @$route.name.match 'edit'
                    @dislabledStatus = false
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
    .edit-manage-container
        margin: 10px 0 10px 0

        &.fixed
            position: fixed
            top: 0
            z-index: 10
</style>