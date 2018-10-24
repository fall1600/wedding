module.exports =
  list:
    id:
      label: 'fields.invitation.id'
      type: 'text'
      sort: true
      defaultSorting: 'desc'
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
    known_from:
      label: 'fields.invitation.known_from'
      type: 'text'
      sort: true
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