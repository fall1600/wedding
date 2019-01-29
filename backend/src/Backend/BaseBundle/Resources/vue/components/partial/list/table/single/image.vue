<template>
    <img :src="imageUrl">
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  props: ['column']
  computed:
    imageUrl:  () ->
      return "" if !@column || @column.master == undefined || @column.master == null
      @column.master.tiny
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
img
  width:  60px
</style>