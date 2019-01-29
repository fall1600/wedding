module.exports = {
  created: () ->
    me = @
    for event of @$options.events
      @$root.$on event, me.$options.events[event]
    return
  beforeDestroy: () ->
    for event of @$options.events
      @$root.$off event, @$options.events[event]
    return
}
