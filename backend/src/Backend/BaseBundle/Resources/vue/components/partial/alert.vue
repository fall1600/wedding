<template></template>
<style src="pnotify/dist/pnotify.history.css"></style>
<style src="pnotify/dist/pnotify.css"></style>
<style src="pnotify/dist/pnotify.buttons.css"></style>

<style lang="sass?indentedSyntax" type="text/sass">
h4.ui-pnotify-title
  text-transform:  capitalize
.ui-pnotify-icon
  >span
    size:   14px
</style>

<script lang="babel!coffee" type="text/coffeescript">
require "pnotify/dist/pnotify.js"
require "pnotify/dist/pnotify.animate.js"
require "pnotify/dist/pnotify.buttons.js"
# @param string message - alert文字
# @param string style - alert style(info, success, warning, danger)
# 範例
# @$root.alert.set '測試alert文字', 'warning'
module.exports =
  mixins:  [require "components/backendbase/mixins/base.coffee"]
  computed:
    alertInfo: () ->
      @$store.getters.alertInfo
  watch:
    alertInfo:
      deep: true
      handler: (newValue) ->
        @alert newValue.style, newValue.message, newValue.title
        return
  created: () ->
    PNotify.prototype.options.styling = "fontawesome"
  methods:
    # 啟動alert, 設定alert文字及style
    alert: (style, message, title) ->
      notify = new PNotify({
        title: title
        text: message
        type: style
        animate:
          animate: true
          in_class: 'flipInX'
          out_class: 'flipOutX'
      })
      notify.get().click () ->
        notify.remove()
      true
</script>
