import token from './token.coffee'
import './request.coffee'
import mockApi from './mockApi.coffee'
import {apibase} from 'static/apibase.json'

class API
  constructor: (@token) ->
    $.requestConfig
      apiBase:  apibase
      overrideMethod: true
  request: (type, url, data = null, withToken = true) ->
    me = @
    return new Promise (resolve, reject) ->
      requestConfig =
        type:     type
        url:      url
        dataType: 'JSON'
        data:      data
        processData: true
        xhrFields: {}
        jsonDataRequest: true
        success:   (result) ->
          resolve result
        error:     (xhr, status, error) ->
          reject [xhr, status, error]
      if me.token.getToken() != null && withToken
        requestConfig.headers =
          Authorization: "Bearer #{me.token.getToken()}"
      $.request requestConfig
  filestream: (type, url, data = null, withToken = true) ->
    me = @
    return new Promise (resolve, reject) ->
      filestreamConfig =
        type:     type
        url:      url
        processData: true
        contentType: 'application/json; charset=utf-8'
        dataType: 'binary'
        xhrFields:
          responseType: 'blob'
        data:      data
        success:   (result, status, xhr) ->
          resolve
            data:  result
            status:  status
            xhr:     xhr
        error:     (xhr, status, error) ->
          reject [xhr, status, error]
      filestreamConfig.headers =
        Authorization: "Bearer #{me.token.getToken()}"
      $.request filestreamConfig
  multipartRequest: (url, data, onProgress = null, withToken = true) ->
    me = @
    return new Promise (resolve, reject) ->
      multipartConfig =
        type: 'POST'
        url:  url
        contentType: false
        processData: false
        xhrFields: {}
        data: data
        dataType: 'json'
        jsonDataRequest: false
        xhr: () ->
          xhr = $.ajaxSettings.xhr()
          xhr.upload.addEventListener 'progress', (progress) ->
            onProgress(progress) if onProgress
          , false
          return xhr
        success: (result) ->
          resolve result
          return
        error: (result) ->
          reject result
          return
      me.token = token if me.token == undefined || me.token == null
      if me.token.getToken() != null && withToken
        multipartConfig.headers =
          Authorization: "Bearer #{me.token.getToken()}"
      $.request multipartConfig
  login: (data) ->
    @request 'PUT', '/login', data
  # 查詢是否安裝產品屬性
  checkStyleBundle: () ->
    new Promise (resolve, reject) ->
      setTimeout () ->
        resolve true
      , 700
  renewToken: () ->
    @request 'PUT', '/renewtoken'

api = new API(token)
#api = mockApi api

export default api