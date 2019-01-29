import session from '../../libs/sessionStorageShare.coffee'
import token from '../../libs/token.coffee'
import api from '../../libs/api.coffee'
import history from '../../libs/history.coffee'
import time from '../../libs/time.coffee'

export default {
  state:
    loading: false
    token: token
    api: api
    history: history
    time: time
    session: session
    trans: null
  mutations:
    "token.setToken": (state, token) ->
      state.token.setToken token
      return
    "token.clearToken": (state) ->
      state.token.clearToken()
      return
    "loading.change": (state, status) ->
      state.loading = status
      return
    'public.trans': (state, trans) ->
      state.trans = trans
  actions:
    "token.setToken": (context, token) ->
      context.commit "token.setToken", token
      return
    "token.clearToken": (context) ->
      context.commit "token.clearToken"
      return
    "loading.change": (context, status) ->
      context.commit "loading.change", status
      return
    'public.trans': (context, trans) ->
      context.commit 'public.trans', trans
  getters:
    trans: (state) ->
      state.trans
    timeFilter: (state) ->
      state.time
    token: (state) ->
      state.token
    api: (state) ->
      state.api
    loading: (state) ->
      state.loading
    history: (state) ->
      state.history
}