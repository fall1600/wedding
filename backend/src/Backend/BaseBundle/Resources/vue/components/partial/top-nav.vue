<template>
    <div class="top_nav hidden-print">
        <div class="nav_menu">
            <nav class="" role="navigation">
                <div class="nav toggle">
                    <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                </div>

                <ul class="nav navbar-nav navbar-right">
                    <li class="logout">
                        <a @click="logoutConfirm()">
                            <i class="fa fa-sign-out"></i>
                            {{"message.logout"|trans}}
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</template>

<script lang="babel!coffee" type="text/coffeescript">
# <script lang="coffee" type="text/coffeescript">
module.exports =
  computed:
    loginName: () ->
      return "" if !@token || !@token.getInfo()
      @token.getInfo().data.login_name
    token: () ->
      @$store.getters.token
  methods:
    logoutConfirm: () ->
      if confirm @$options.filters.trans 'logout.alert.confirm'
        @$router.push
          name: "logout"
</script>
<style lang="sass?indentedSyntax" type="text/sass" scoped>
@import "../assets/sass/base.sass"
.page-title
  @extend %clearfix
.logout
  cursor: pointer
</style>