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
    type: 'input'
    required: true
  },
  {
    name: 'number_of_vegetarian'
    text: 'form.label.edit.invitation.number_of_vegetarian'
    type: 'input'
    required: true
  },
  {
    name: 'number_of_baby_seat'
    text: 'form.label.edit.invitation.number_of_baby_seat'
    type: 'input'
  },
  {
    name: 'address'
    text: 'form.label.edit.invitation.address'
    type: 'input'
  },
  {
    name: 'email'
    text: 'form.label.edit.invitation.email'
    type: 'input'
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
    name: 'note'
    text: 'form.label.edit.invitation.note'
    type: 'textarea'
  }
]
