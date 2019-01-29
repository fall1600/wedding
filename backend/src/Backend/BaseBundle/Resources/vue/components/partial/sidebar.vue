<template>
    <div class="left_col scroll-view">
        <sidebar-menu></sidebar-menu>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  components:
    'sidebar-menu':  require "components/backendbase/partial/sidebar/menu.vue"
</script>


<style lang="sass?indentedSyntax" type="text/sass" scoped>
.left_col
  width:    100%
</style>