module.exports =
{
  "list": {
    "id": {
      "label": "#",
      "type": "id",
      "sort": true
      short: true
    },
    "login_name": {
      "label": "index.site_user.login_name",
      "type": "text",
      "sort": true,
      "quick": true,
      "search": true
      searchConfig:
        key: "login_name"
        like: true
        type: "text"
    },
    "email": {
      "label": "index.site_user.email",
      "type": "text",
      "sort": true,
      "quick": true,
      "search": true
      searchConfig:
        key: "email"
        like: true
        type: "text"
    },
    "site_groups": {
      "label": "index.site_user.site_groups",
      "type": "relational",
      config:
        labelKey: "name"
        max: 20

    },
    "enabled": {
      "label": "index.site_user.enabled",
      "type": "checkbox",
      "batch": true
      config:
        value:
          true: 1
          false: 0
      batchSetting: [
        { label: 'index.site_user.enable', value: true }
        { label: 'index.site_user.disable', value: false }
      ]
    },
    "updated_at": {
      "label": "index.site_user.last_login",
      "type": "datetime-local",
      "sort": true
    },
    "created_at": {
      "label": "index.site_user.created_at",
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
      "route": 'siteuser-new'
    }
  ],
  "action": [
    {
      "name": "edit",
      "label": "action.edit",
      "roles": ["ROLE_SUPERADMIN"],
      "route": 'siteuser-edit'
    }
  ]
}