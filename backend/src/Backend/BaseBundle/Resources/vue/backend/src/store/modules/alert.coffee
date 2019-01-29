export default {
  state:
    title: ""
    message: ""
    style: "success"
    _seq: {}
  mutations:
    alert: (state, data) ->
      state.title = data.title
      state.style = data.style
      state.message = data.message
      state._seq = new Date()
      return
  actions:
    alert: (context, data) ->
      context.commit "alert", data
      return
  getters:
    "alertInfo": (state) ->
      state
}