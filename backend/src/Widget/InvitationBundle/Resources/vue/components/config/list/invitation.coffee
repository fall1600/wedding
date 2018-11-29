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
    phone:
      label: 'fields.invitation.phone'
      type: 'text'
      sort: true
      search: true
      searchConfig:
        key: 'phone'
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
          true: "male"
          false: "female"
    categories:
      label: 'fields.invitation.categories'
      type: 'customize'
      config:
        component: require 'components/widgetinvitation/router/list/_friends.vue'
      search: true
      searchConfig:
        key: 'friend_relation.category_id'
        type: 'select'
        api: 'invitation.getFriends'
        choiceValue: 'id'
        choiceLabel: 'name'
    updated_at:
      defaultSorting: 'desc'
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