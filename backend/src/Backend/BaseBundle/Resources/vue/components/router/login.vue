<template>
    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                <form>
                    <h1>{{ "message.backend.login.info"|trans }}</h1>
                    <div>
                        <input class="form-control username" placeholder="Username / Email" required="" type="text" v-model="form.username">
                    </div>
                    <div>
                        <input class="form-control password" placeholder="Password" required="" type="password" v-model="form.password">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-default submit" href="" @click.prevent="formSubmit">{{ "message.backend.login.backend"|trans }}</button>
                        <!--<a class="reset_pass" href="#">{{ "message.backend.login.forgetpassword"|trans }}</a>-->
                    </div>
                    <div class="clearfix"></div>
                    <div class="separator"></div>
                </form>
            </section>
        </div>
    </div>
</template>


<script lang="babel!coffee" type="text/coffeescript">
module.exports =
  computed:
    token: () ->
      @$store.getters.token.getToken()
  watch:
    token:
      deep:   true
      handler: (newValue, oldValue) ->
        if newValue != null
          @redirectToPrev()
  mounted: () ->
    @$store.dispatch 'pageTitle.update', '登入'
  methods:
    redirectToPrev: () ->
      route = '/'
      if @$route.query.path != undefined
        route = @$route.query.path
      @$router.replace route
    formSubmit: () ->
      @$store.dispatch "token.clearToken"
      me = @
      @$store.state.base.api.login(@form)
      .then (data) ->
        me.$store.dispatch "token.setToken", data.token
        me.$store.dispatch "alert",
          style: "success"
          title: me.$options.filters.trans 'login.alert.success.title'
          message: me.$options.filters.trans 'login.alert.success.message'
        me.redirectToPrev()
        return
      .catch (err) ->
        return if err[0] == undefined
        switch err[0].status
          when 404
            me.$store.dispatch "alert",
              style: 'error'
              title: me.$options.filters.trans 'login.alert.user_not_found.title'
              message: me.$options.filters.trans 'login.alert.user_not_found.message'
          when 403
            me.$store.dispatch "alert",
              style: 'error'
              title: me.$options.filters.trans 'login.alert.password_fail.title'
              message: me.$options.filters.trans 'login.alert.password_fail.message'
          else
            me.error = "message.backend.login.err.#{err[0].status}"
            me.$store.dispatch "alert",
              style: "error"
              title: me.$options.filters.trans "message.backend.login.err.title"
              message: "#{me.$options.filters.trans('message.backend.login.err.message')} #{err[0].status}"
  data: () ->
    form:
      username: ''
      password: ''
    error: null
</script>