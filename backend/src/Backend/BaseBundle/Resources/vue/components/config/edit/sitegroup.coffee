module.exports =
[
  {
    "name": "name",
    "text": "form.label.edit.site_group.name",
    "type": "input",
    "required": true
  },
  {
    "name": "site_users",
    "text": "form.label.edit.site_group.users",
    "type": "selector",
    dataType: 'string_array'
    "config":
      api: "sitegroup.getSiteUsers"
      value: "id"
      label: "login_name"
  },
  {
    "name": "default_roles",
    "text": "form.label.edit.site_user.roles",
    "type": "checkboxgroup",
    "config": {
      api: "sitegroup.allRoles"
      group_label_postfix: "type"
      group_label: "name"
      group_child: "child"
    }
  }
]