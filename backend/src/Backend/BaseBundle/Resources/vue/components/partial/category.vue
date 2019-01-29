<template>
    <div class="category-container">
        <!--<tree v-if="startRender"></tree>-->
        <tree :useCustomize="useCustomize"></tree>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
    props: ['useCustomize']
    mounted: () ->
    components:
        'tree': require 'components/backendbase/partial/category/tree.vue'
    computed:
        startRender: () ->
            return @$store.getters.categoryStartRender
</script>