import Vue from 'vue'
import VueHead from 'vue-head'
import VueRouter from 'vue-router'
import VueMoment from 'vue-moment'
import cm4RouterConfig from './cm4/routers.coffee'
import routerConfig from '../cm4/routers.coffee'
import lang from './libs/lang.coffee'
import './libs/translation.coffee'
import store from './store/store.coffee'
import app from './app.vue'
import dispatcher from './libs/dispatcher.coffee'
import meta from "assets/backendbase/meta/default.json"
import vue2Filter from 'vue2-filters'
isDebugMode = process.env.NODE_ENV != 'production'
Vue.config.debug = isDebugMode
Vue.config.devtools = isDebugMode

Vue.use VueHead
Vue.use VueRouter
Vue.use VueMoment
Vue.use vue2Filter
router = new VueRouter
  hashbang: true
  mode: 'hash'
  routes: cm4RouterConfig.concat routerConfig

window.rootComponent = new Vue
  el:    "app"
  store: store
  computed:
    api: () ->
      @$store.getters.api
    token: () ->
      @$store.getters.token
  created: () ->
    @$store.dispatch 'public.trans', @$options.filters.trans
  mounted: () ->
    me = @
    @token.renewToken = () ->
      me.api.renewToken()
      .then (data) ->
        me.$store.dispatch "token.setToken", data.token
      .catch () ->
        me.$store.dispatch "token.clearToken"
        return
    if @token.getToken() == null && @$route.meta['require-login'] != false
      @$router.push
        name: 'login'
        query:
          path: @$route.path
  data: () ->
    _: require('lodash')
    {
      dispatcher: dispatcher @
      lang: lang
      head: meta
    }
  router: router
  render: (h) ->
    h app
  head:
    title: () ->
      {
        inner:  @head.title
        separator: ' '
        complement: ' '
      }
    meta: () ->
      @head.meta