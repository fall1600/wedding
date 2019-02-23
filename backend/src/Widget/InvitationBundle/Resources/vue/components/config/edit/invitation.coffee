module.exports =
[
  {
    name: 'name'
    text: 'form.label.edit.invitation.name'
    type: 'input'
    required: true
  },
  {
    name: 'nickname'
    text: 'form.label.edit.invitation.nickname'
    type: 'input'
  },
  {
    name: 'phone'
    text: 'form.label.edit.invitation.phone'
    type: 'input'
    required: true
  },
  {
    name: 'number_of_people'
    text: 'form.label.edit.invitation.number_of_people'
    type: 'number'
    required: true
    default: 1
  },
  {
    name: 'number_of_vegetarian'
    text: 'form.label.edit.invitation.number_of_vegetarian'
    type: 'number'
  },
  {
    name: 'number_of_baby_seat'
    text: 'form.label.edit.invitation.number_of_baby_seat'
    type: 'number'
  },
  {
    name: 'address'
    text: 'form.label.edit.invitation.address'
    type: 'input'
  },
  {
    name: 'is_sent_address'
    text: 'form.label.edit.invitation.is_sent_address'
    type: "switch"
    config: {
      on: 'form.choice.yes'
      off: 'form.choice.no'
    }
  },
  {
    name: 'email'
    text: 'form.label.edit.invitation.email'
    type: 'input'
  },
  {
    name: 'is_sent_email'
    text: 'form.label.edit.invitation.is_sent_email'
    type: "switch"
    config: {
      on: 'form.choice.yes'
      off: 'form.choice.no'
    }
  },
  {
    name: 'attend'
    text: 'form.label.edit.invitation.attend'
    type: 'choice'
    required: true
    config:
      api:  "invitation.getAllAttends"
      value: "value"
      label: "key"
  },
  {
    name: 'known_from'
    text: 'form.label.edit.invitation.known_from'
    type: 'choice'
    required: true
    config:
      api: 'invitation.getAllKnownFrom'
      value: 'value'
      label: 'key'
  },
  {
    name: "categories"
    text: 'form.label.edit.invitation.categories'
    type: "selector"
    dataType: 'string_array'
    config:
      api: "invitation.getFriends"
      value: "id"
      label: "name"
  },
  {
    name: 'note'
    text: 'form.label.edit.invitation.note'
    type: 'textarea'
  }
]
