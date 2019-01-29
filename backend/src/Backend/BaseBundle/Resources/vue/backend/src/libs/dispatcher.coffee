class Dispatcher
  constructor: (@app) ->
    @event = ''
    @config = []
  set: (@event, @config) ->
    switch @event
#      會自動收起來的alert
#      範例:
#      config =
#        message: '測試alert文字'
#        style: 'warning'
#      @$root.dispatcher.set 'alert-toggle', config
      when 'alert-toggle'
        @app.alert.set @config.message, @config.style
        break
    return
export default (app) ->
  return new Dispatcher(app)
