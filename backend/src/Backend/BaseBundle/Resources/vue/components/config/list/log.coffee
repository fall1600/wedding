module.exports =
{
  "list": {
    "id": {
      "label": "#",
      "type": "id",
      "sort": true
    },
    "site_user.login_name": {
      "label": "index.operation_log.login_name",
      "type": "text",
      "sort": true,
    },
    "modify_table": {
      "label": "index.operation_log.modify_table",
      "type": "text",
      "config": {
        label_prefix: "index_value.operation_log.modify_table"
        replace:
          match: 'index_value.operation_log.modify_table.loginname'
          newString: 'index_value.operation_log.modify_table.login'
      }
    },
    "modify_type": {
      "label": "index.operation_log.modify_type",
      "type": "text",
      "config": {
        label_prefix: "index_value.operation_log.modify_type"
      }
    },
    "created_at": {
      "label": "index.operation_log.created_at",
      "type": "datetime-local",
      "sort": true
      "defaultSorting": 'desc'
    }
  },
  "extra": [
  ],
  "action": [
    {
      "name": "view",
      "label": "action.view",
      "roles": ["ROLE_SUPERADMIN"],
      "route": 'log-view'
    },
    {
      "name": "delete",
      "roles": ["ROLE_SUPERADMIN"],
      "component": require 'components/backendbase/partial/list/table/actions/delete.vue'
    }
  ]
}