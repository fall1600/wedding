<template>
    <div class="page-content">
        <!--列表元件-->
        <list></list>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
    module.exports =
        mounted: () ->
            @$store.dispatch 'pageTitle.update', '帳號列表'
        mixins:  [require "components/backendbase/mixins/listConfig.coffee"]
        methods:
            listConfig: () ->
                require "components/backendbase/actions/list/siteuser.coffee"
            routerChange: () ->
                @init()
            init: () ->
                return
</script>