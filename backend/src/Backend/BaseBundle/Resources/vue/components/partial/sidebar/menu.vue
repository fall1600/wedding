<template>
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu-loading" v-if="!isRender" v-for="i in 5"></div>
        <div class="menu_section" v-show="isRender">
            <ul class="nav side-menu">
                <li :item-id="item.route" :class="{'current-page': nowpage == item.route}" v-for="(item, name) in menuConfig" v-if="(hasChild(item.route) && typeof item.route == 'object') || (typeof item.route == 'string' && token.hasRole(item.role))">
                    <router-link :to="{name: item.route}" v-if="typeof item.route == 'string' && token.hasRole(item.role)" v-show="item.hide != true">
                        <i :class="item.icon"></i>
                        <span class="nav-label">{{name|trans}}</span>
                    </router-link>
                    <a v-if="typeof item.route == 'object'">
                        <i :class="item.icon"></i> {{name|trans}} <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                        <template v-for="(subItem, subName) in item.route">
                            <li class="group-label" v-if="subItem.group_label != undefined">
                                <a>{{subItem.group_label|trans}}</a>
                            </li>
                            <li :sub-item-id="subItem.route" :class="{'current-page': nowpage == subItem.route}" v-if="token.hasRole(subItem.role)" v-show="subItem.hide != true && renderDynamic(subItem.isDynamic, subName)">
                                <router-link :to="{name: subItem.route}">{{subName|trans}}</router-link>
                            </li>
                        </template>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  mounted: () ->
    me = @
    if @dynamicConfig != undefined
      if@dynamicConfig.length == 0
        @renderMenu()
        return
    promises = []
    for config in @dynamicConfig
      apiName = @api
      for node in config.api.split('.')
        apiName = apiName[node]
      promises.push apiName()

    # 發api取得動態產生條件, 並將產生menu條件同步到store上
    promiseIndex = 0
    promises.map (promise) ->
      promise.then (result) ->
        me.$store.dispatch 'menu.update.dynamic',
          key: me.dynamicConfig[promiseIndex].menu
          isShow: result == me.dynamicConfig[promiseIndex].equal
        promiseIndex++
      promise.catch () ->
        console.warn '動態menu的api有問題'

    # 檢查完所有的動態產生menu
    Promise.all(promises)
    .then () ->
      me.renderMenu()
    .catch () ->
      me.renderMenu()
  computed:
    api: () ->
      @$store.getters.api
    token: () ->
      @$store.getters.token
    nowpage: ->
      @$route.name
    dynamicMenu: () ->
      @$store.getters.dynamicMenu
  data: () ->
    menuConfig: require 'menu.coffee'
#    rules: require './rules.coffee'
    rules: require '../../config/env/menuBundle.json'
    isRender: false
    dynamicConfig: require './dynamic.coffee'
  methods:
    hasChild: (group) ->
      children = 0
      for key of group
        if group[key].role != undefined && group[key].hide != true
          result = @token.hasRole group[key].role
          if result == true
            children++
      return true if children > 0
      return false
    renderMenu: () ->
      for key of @menuConfig
        if typeof @menuConfig[key].route != 'object'
          delete @menuConfig[key]['hide']
          if @rules[key] == false
            @menuConfig[key].hide = true
        else
          for subKey of @menuConfig[key].route
            delete @menuConfig[key]['route'][subKey]['hide']
            if @rules[subKey] == false
              @menuConfig[key]['route'][subKey].hide = true
      @isRender = true
    renderDynamic: (isDynamic, key) ->
      return true if isDynamic == undefined
      return @dynamicMenu[key]
</script>


<style lang="sass?indentedSyntax" type="text/sass" scoped>
    @import "../assets/sass/base.sass"
    .menu-loading
        margin: auto
        margin-top: 20px
        width: 70%
        height: 10px
        background-color: #3e5c79
        border-radius: 5px
        opacity: 0.7
    .nav.side-menu
        > li
            +transition(.2s)
            > a
                +transition(.2s)
                margin-bottom: 1px
            &.current-page, &.active
                border-right: 0
                box-shadow: inset -4px 0 0 0 $theme-color
                background: rgba(black, .1)
            &.active
                > a
                    background: rgba(color_main('black'), .5)
            @at-root .nav-sm &.active
                &, &-sm
                    border-right: 0
                    box-shadow: inset 4px 0 0 0 $theme-color
                    background: rgba(black, .1)
    .nav-md ul.nav.child_menu
        $sub-link-color: #192632
        > li
            background-color: $sub-link-color
            a
                padding-left: 20px
            &.group-label
                $group-label-color: #121b24
                cursor: auto
                background-color: $group-label-color
                box-shadow: inset -4px 0 0 0 $group-label-color
                &:before, &:after
                    display: none
                a
                    padding-left: 9px
                    cursor: auto
            &:before
                +transition(.2s)
            &.current-page
                &:before
                    background: $theme-color
</style>
