module.exports =
  methods:
    setGrid: (grid) ->
      if grid != undefined
        return grid
      return 'col-xs-12'