module.exports =
{
  "list": {
    "id": {
      "label": "#"
      "type": "id"
      "sort": true
    }
    "name": {
      "label": "index.photo_config.name"
      "type": "text"
      "sort": true
      "quick": true
    }
    "created_at": {
      "label": "index.photo_config.created_at"
      "type": "datetime-local"
      "sort": true
      defaultSorting: 'desc'
    }
  }
  "extra": [
    {
      "name": "new"
      "label": "form.button.new"
      "roles": ["ROLE_PHOTO_CONFIG_WRITE"]
      "route": 'photoconfig-new'
    }
  ]
  "action": [
    {
      "name": "edit"
      "label": "action.edit"
      "roles": ["ROLE_PHOTO_CONFIG_WRITE"]
      "route": 'photoconfig-edit'
    }
    {
      "name": "delete"
      "label": "action.delete"
      "roles": ["ROLE_PHOTO_CONFIG_WRITE"]
      "component": require 'components/backendbase/partial/list/table/actions/delete.vue'
    }
  ]
}