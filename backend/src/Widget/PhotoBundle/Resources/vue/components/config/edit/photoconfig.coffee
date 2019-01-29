module.exports =
[
  {
    "name": "name",
    "text": "form.label.edit.photo_config.name",
    "required": true,
    "type": "input"
  },
  {
    "name": "brief",
    "text": "form.label.edit.photo_config.brief",
    "required": true,
    "type": "input"
  },
  {
    "name": "crop",
    "text": "form.label.edit.photo_config.crop",
    "type": "switch",
    "config": {
      true: "form.choice.yes"
      false: "form.choice.no"
    }
  },
  {
    name: "config"
    text: "form.label.edit.photo_config.config"
    type: "repeatedform"
    allowAdd: true
    config: [
      {
        name: "type"
        text: "form.label.edit.photo_config_item.type"
        type: "choice"
        grid: 'col-xs-10'
        required: true
        config:
          api: "photoconfig.getChoice"
      }
      {
        name: "suffix"
        text: "form.label.edit.photo_config_item.suffix"
        grid: 'col-xs-10'
        required: true
        type: "input"
      }
      {
        name: "width"
        text: "form.label.edit.photo_config_item.width"
        grid: 'col-xs-10'
        required: true
        type: "number"
        notNull: true
      }
      {
        name: "height"
        text: "form.label.edit.photo_config_item.height"
        grid: 'col-xs-10'
        required: true
        type: "number"
        notNull: true
      }
    ]
  },
]