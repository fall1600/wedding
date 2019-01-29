import queryString from 'qs'

class History
  constructor: () ->
  getFilterQuery: (route) ->
    history = JSON.parse sessionStorage.getItem('filter')
    return null if history == null || history == undefined
    return null if history[route] == null || history[route] == undefined

    historyFilter = history[route]
    query = {}
    query.search = queryString.stringify historyFilter.search
    query.sort = queryString.stringify historyFilter.sort
    query.page = historyFilter.page
    return query

export default new History()