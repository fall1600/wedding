webpage = require "webpage"
system = require "system"
fs = require "fs"

fetchUrl = (url, cb) ->
  responseStatus = false
  page = webpage.create()

  _pageDone = () ->
    cb true, page.content, "pageDone"
    phantom.exit()
    return

  _pageError = (data) ->
    cb false, page.content, "pageError", data.statusCode
    phantom.exit()
    return

  page.open url, (status) ->
    responseStatus = true if status == 'success'
    return

  page.onCallback = (data) ->
    if data == 'pageDone'
      _pageDone()
      return
    if typeof data == 'object'
      if data.statusCode != undefined
        _pageError data
        return

  setTimeout () ->
    if responseStatus
      cb true, page.content, "timeout"
    else
      cb false, null
    phantom.exit()
  , 10000 # wait for 10 secs
  return

url = system.args[2]
path = system.args[1]

fetchUrl url, (status, content, log = null, statusCode = 200) ->
  fs.write path, JSON.stringify
    status: status
    statusCode: statusCode
    content: content
    log: log
  , 'w'

