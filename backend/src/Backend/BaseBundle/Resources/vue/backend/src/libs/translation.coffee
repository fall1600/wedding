import Vue from 'vue'
import lang from './lang.coffee'
import config from "translations/config.json"
init = () ->
  locale = lang.getLang()
  if config.locales.indexOf(locale) < 0
    locale = config.fallback
    lang.setLang locale
  translationText = {}
  try
    for catalogue in ["forms", "validators"]
      translationText = Object.assign(translationText, require("translations/#{catalogue}.#{locale}.json"))
  catch e
    console.log e.message

  Vue.filter 'trans', (text) ->
    if translationText[text]?
      return translationText[text]
    return text
  return ''

export default init()