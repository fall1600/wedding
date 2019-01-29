<template>
    <div></div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  mounted: () ->
      @$store.dispatch 'pageTitle.update', 'Home'
</script>