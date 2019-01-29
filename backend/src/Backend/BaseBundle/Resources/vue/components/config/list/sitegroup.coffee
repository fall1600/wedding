module.exports =
{
  "list": {
    "id": {
      "label": "#",
      "type": "id",
      "sort": true
    },
    "name": {
      "label": "index.site_group.name",
      "type": "text",
      "sort": true,
      "quick": true
    },
    "created_at": {
      "label": "index.site_group.created_at",
      "type": "datetime-local",
      "sort": true
      defaultSorting: 'desc'
    }
  },
  "extra": [
    {
      "name": "new"
      "label": "form.button.new"
      "roles": ["ROLE_SUPERADMIN"]
      "route": 'sitegroup-new'
    }
  ],
  "action": [
    {
      "name": "edit",
      "label": "action.edit",
      "roles": ["ROLE_SUPERADMIN"],
      "route": 'sitegroup-edit'
    },
    {
      "name": "delete",
      "roles": ["ROLE_SUPERADMIN"],
      "component": require 'components/backendbase/partial/list/table/actions/delete.vue'
    }
  ]
}