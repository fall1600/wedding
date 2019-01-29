module.exports =
[
  {
    "name": "login_name",
    "text": "form.label.edit.site_user.login_name",
    "type": "input",
    readonly: true
    "required": true
  },
  {
    "name": "first_name",
    "text": "form.label.edit.site_user.first_name",
    "type": "input",
    "required": true
  },
  {
    "name": "last_name",
    "text": "form.label.edit.site_user.last_name",
    "type": "input",
    "required": true
  },
  {
    "name": "email",
    readonly: true
    "text": "form.label.edit.site_user.email",
    "type": "input",
    "required": true
  },
  {
    "name": "enabled",
    "text": "form.label.edit.site_user.enable",
    "type": "switch",
    "config": {
      on: 'form.choice.enable'
      off: 'form.choice.disable'
    }
  },
  {
    "name": "plain_password",
    "text": "form.label.edit.site_user.plain_password",
    "type": "repeated",
    "config": {
      "type": "password"
    }
  },
  {
    "name": "site_groups",
    "text": "form.label.edit.site_user.groups",
    "type": "selector",
    dataType: 'string_array'
    "config":
      api: "siteuser.getSiteGroups"
      value: "id"
      label: "name"
  },
  {
    "name": "default_roles",
    "text": "form.label.edit.site_user.roles",
    "type": "checkboxgroup",
    "config": {
      api: "siteuser.allRoles"
      group_label_postfix: "type"
      group_label: "name"
      group_child: "child"
    }
  }
]