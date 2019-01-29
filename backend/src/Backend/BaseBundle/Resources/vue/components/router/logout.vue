<template></template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  methods:
    redirectToLogin: () ->
      @$store.dispatch 'alert',
          style: 'success'
          title: @$options.filters.trans 'logout.alert.title'
          message: @$options.filters.trans 'logout.alert.message'
      @$router.push
        name: "login"
  mounted: () ->
      @$store.dispatch "token.clearToken"
      @redirectToLogin()
      window.location.reload()
</script>