module.exports = {
  mixins:  [
    require "components/backendbase/mixins/base.coffee"
    require "components/backendbase/mixins/lists.coffee"
  ]
  components:
    'pagination': require "components/backendbase/partial/pagination.vue"
    'list-manage': require "components/backendbase/partial/list/manage.vue"
    'list-table': require "components/backendbase/partial/list/table.vue"
    'list-search': require "components/backendbase/partial/list/search.vue"
}