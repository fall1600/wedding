module.exports =
  list:
#    id:
#      label: 'fields.invitation.id'
#      type: 'text'
#      sort: true
#      defaultSorting: 'desc'
    name:
      label: 'fields.invitation.name'
      type: 'text'
      sort: true
      search: true
      searchConfig:
        key: 'name'
        type: 'text'
        like: true
    attend:
      label: 'fields.invitation.attend'
      type: 'text'
      sort: true
      search: true
      searchConfig:
        key: 'attend'
        type: 'select'
        api: 'invitation.getAllAttends'
        choiceValue: 'value'
        choiceLabel: 'key'
    known_from:
      label: 'fields.invitation.known_from.title'
      type: 'text'
      sort: true
      search: true
      searchConfig:
        key: 'known_from'
        type: 'radio'
        label:
          true: 'fields.invitation.known_from.male'
          false: 'fields.invitation.known_from.female'
        value:
          true: "男方"
          false: "女方"
  extra: [
    {
      "name": "new"
      "label": "form.button.new"
      "roles": ["ROLE_INVITATION_WRITE"]
      "route": 'invitation-new'
    }
  ]
  action: [
    {
      name: 'edit'
      label: 'action.edit'
      roles: ['ROLE_INVITATION_WRITE']
      route: 'invitation-edit'
    }
    {
      name: 'delete'
      label: 'action.delete'
      roles: ['ROLE_INVITATION_WRITE']
      component: require 'components/backendbase/partial/list/table/actions/delete.vue'
    }
  ]