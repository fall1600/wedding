<template>
  <div data-role="switch" :class="config">
    <div class="switch" v-if="style == 'round' || style == undefined">
      <input :id="'round-'+uid" class="switch-toggle switch-toggle-round" type="checkbox"
        v-model="localValue" @change="change()" />
      <label :for="'round-'+uid"></label>
    </div>

    <div class="switch" v-if="style == 'round-flat'">
      <input :id="'round-flat -'+uid" class="switch-toggle switch-toggle-round-flat" type="checkbox"
        v-model="localValue"  @change="change()" />
      <label :for="'round-flat -'+uid"></label>
    </div>
  </div>
</template>

<script>
  var uuid = require('uuid/v4');
  module.exports = {
    props: ['value', 'style', 'size'],
    mounted: function mounted() {
      this.init();
    },
    methods: {
      init: function init() {
        this.localValue = this.value;
        if (this.value == undefined) this.localValue = false;
      },
      change: function change() {
        this.$emit('change');
      }
    },
    data: function data() {
      return {
        localValue: false
      };
    },
    computed: {
      uid: function uid() {
        return uuid();
      },
      config: function config() {
        if (this.size == undefined) return 'normal';
        return this.size;
      }
    },
    watch: {
      localValue: function localValue() {
        this.$emit('input', this.localValue);
      },
      value: function value() {
        this.localValue = this.value;
      }
    }
  };
</script>

<style lang="sass?indentedSyntax" type="text/sass" scoped>
$configs: ('large': 35px, 'normal': 25px, 'small': 20px)
$default-on-color: #8ce196
$default-off-color: #dddddd
$default-border-color: #fff
@each $config, $size in $configs
  .#{$config}[data-role="switch"]
    height: $size
    display: inline-block

  .#{$config}[data-role="switch"]
  	.switch
      display: inline-block
      position: relative
      .switch-toggle
        position: absolute
        margin-left: -9999px
        visibility: hidden
        & + label
          display: block
          position: relative
          cursor: pointer
          outline: none
          user-select: none

      .switch-toggle-round
        & + label
          padding: 2px
          width: $size*2
          height: $size
          background-color: $default-on-color
          border-radius: $size
          &:before, &:after
            display: block
            position: absolute
            top: 1px
            left: 1px
            bottom: 1px
            content: ''
          &:before
            right: 1px
            background-color: #f1f1f1
            border-radius: $size
            transition: background 0.4s
          &:after
            width: $size - 2px
            background-color: $default-border-color
            border-radius: 100%
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3)
            transition: margin 0.4s
        &:checked + label
          &:before
            background-color: $default-on-color
          &:after
            margin-left: $size

      .switch-toggle-round-flat
        & + label
          padding: 2px
          width: $size*2
          height: $size
          background-color: $default-off-color
          border-radius: $size
          transition: background 0.4s
          &:before, &:after
            display: block
            position: absolute
            content: ''
          &:before
            top: 2px
            left: 2px
            bottom: 2px
            right: 2px
            background-color: $default-border-color
            border-radius: $size
            transition: background 0.4s
          &:after
            top: 4px
            left: 4px
            bottom: 4px
            width: $size - 8px
            background-color: $default-off-color
            border-radius: $size - 8px
            transition: margin 0.4s, background 0.4s
        &:checked + label
          background-color: $default-on-color
          &:after
            margin-left: $size
            background-color: $default-on-color

      .switch-toggle-flat
        & + label
          padding: 2px
          width: $size*2
          height: $size
          &:before, &:after
            display: block
            position: absolute
            top: 0
            left: 0
            bottom: 0
            right: 0
            color: $default-border-color
            font-size: 16px
            text-align: center
            line-height: $size
          &:before
            background-color: $default-off-color
            content: attr(data-off)
            transition: transform 0.5s
            backface-visibility: hidden
          &:after
            background-color: $default-on-color
            content: attr(data-on)
            transition: transform 0.5s
            transform: rotateY(180deg)
            backface-visibility: hidden
        &:checked + label
          &:before
            transform: rotateY(180deg)
          &:after
            transform: rotateY(0)
</style>