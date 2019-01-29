class Lang
  _readFromStorage: () ->
    try
      localLang = window.localStorage.getItem 'lang'
      return localLang
    catch e
      window.localStorage.setItem 'lang',''
      return ''
  _convertSubLocale: (lang) ->
    regx = /^([a-z]+)\-([a-z]+)$/i
    match = lang.match regx
    return lang if !match
    "#{match[1]}_#{match[2]}"
  constructor: () ->
    if @getLang() == ''
      @setLang @_convertSubLocale navigator.language
  setLang: (lang) ->
    window.localStorage.setItem 'lang', lang
    return
  getLang: () ->
    return @_readFromStorage()
lang = new Lang()

export default lang


