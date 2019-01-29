module.exports =
  methods:
    setGrid: (grid) ->
      if grid != undefined
        return grid
      return 'col-xs-12'
    labelGrid: (config) ->
      if config != undefined
        return config
      return 'col-md-4 col-sm-4 col-xs-12'
    toolGrid: (config) ->
      if config != undefined
        return config
      return 'col-md-8 col-sm-8 col-xs-12'
    mergeClass: (required, grid) ->
      if required == undefined || required == '' || required == null
        return grid
      return "#{grid} #{required}"