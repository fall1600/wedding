<template>
  <div>
    {{ wording }}
  </div>
</template>
<script lang="babel!coffee" type="text/coffeescript">
  module.exports =
    props: ['index', 'dataId']
    computed:
      listData: () -> @$store.getters.listData
      friends: () -> @listData[@index].categories
      wording: () ->
        (_.map @friends, "name").join(", ")
</script>