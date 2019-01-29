<template>
  <span>
    {{data|moment('YYYY-MM-DD HH:mm:ss')}}
  </span>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  props: ['data']
</script>