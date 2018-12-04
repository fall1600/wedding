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
    name: "desk_number"
    text: 'form.label.edit.invitation.desk_number'
    type: "choice"
    config:
      api: "invitation.getDesks"
      value: "id"
      label: "name"
  },
  {
    name: 'note'
    text: 'form.label.edit.invitation.note'
    type: 'textarea'
  }
]
